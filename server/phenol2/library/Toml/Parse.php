<?php

namespace Toml;

class Parser {

    private $in;
    private $out;

    private $currentGroup;
    private $currentLinenumber = 1;

    public static function parse ($input)
    {
        $p = new self($input);

        return $p->out;
    }

    public static function parseFile ($input)
    {
        if (is_file($input) && is_readable($input)) {
            $input = file_get_contents($input);
        } else {
            throw new \InvalidArgumentException("Could not open TOML file '".$input."'.");
        }

        return self::parse($input);
    }

    private function __construct ($input)
    {
        // Splitting at the last \n before '=', '[' or # 
        $this->in = preg_split('/\r\n|\r|\n(?=\s*\w+\s*=|\[|\n|#.*)/s', $input);
        $this->currentGroup = &$this->out;

        foreach ($this->in as &$row)
        {
            $this->parseLine($row);
            $this->currentLinenumber += (1 + substr_count($row, "\n"));
        }
    }

    private function parseLine (&$row)
    {
        // Removing comments
        $line = preg_replace('/#(?=(?:(?:[^"]*+"){2})*+[^"]*+\z).*/', '', $row);
        $line = trim($line);

        if (empty($line)) {
            // An empty line will leave the current key group
            $this->currentGroup = &$this->out;
            return;
        }

        $row = $line;

        // Parse data
        if (preg_match('/^(\S+)\s*=\s*(.*)$/s', $row, $match))
        {
            if (isset($this->currentGroup[$match[1]])) {
                throw new \Exception("Duplicate entry found for '".$row."' on line ".$this->currentLinenumber.".");
                return;
            }

            $this->currentGroup[$match[1]] = $this->parseValue($match[2]);
            return;
        }

        // Create key group
        if (preg_match('/^\[([^\]]+)\]$/s', $line, $matches))
        {
            $m = explode('.', $matches[1]);
            $group = &$this->out[$m[0]];

            for ($i=1; $i<count($m); $i++)
            {
                $group = &$group[$m[$i]];
            }

            $this->currentGroup = &$group;
            return;
        }

        throw new \UnexpectedValueException("Invalid TOML syntax '".$row."' on line ".$this->currentLinenumber.".");
    }

    private function parseValue ($value)
    {
        $value = trim($value);

        if ($value === "") throw new \UnexpectedValueException("Value cannot be empty on line ".$this->currentLinenumber);

        // Parse bools
        if ($value === 'true' || $value === 'false') {
            return $value === 'true';
        }

        // Parse floats
        if (preg_match('/^\-?\d*?\.\d+$/', $value)) {
            return (float) $value;
        }

        // Parse integers
        if (preg_match('/^\-?\d*?$/', $value)) {
            return (int) $value;
        }

        // Parse datetime
        if (strtotime($value)) {
            return $date = new \Datetime($value);
        }

        // Parse string
        if (preg_match('/^"(.*)"$/u', $value, $match)) {
            return $this->parseString($match[1]);
        }

        // Parse arrays
        if (preg_match('/^\[(.*)\]$/s', $value, $match)) {
            return $this->parseArray($match[1]);
        }

        throw new \UnexpectedValueException("Data type '".$value."' not recognized on line ".$this->currentLinenumber.".");
    }


    private function parseArray ($arr)
    {
        if (preg_match_all('/(?<=\[)[^\]]+(?=\])/s', $arr, $m)) {
            // Match nested Arrays
            $values = $m[0];
        } else {
            // We couldn't find any, so we assume it's a regular flat Array
            $values = preg_split('/,(?=(?:(?:[^"]*+"){2})*+[^"]*+\z)/s', $arr);
        }

        // If the $values Array is not greater than 2, $arr is a single value,
        // so we parse and return it to break the recursion
        if (count($values) <= 1) return $this->parseValue($arr);

        $prevType = '';

        // Iterate through nested Arrays...
        foreach ($values as &$sub)
        {
            // ... and parse them for more nested Arrays
            $sub = $this->parseArray($sub);

            // Don't allow mixing of data types in an Array
            if (empty($prevType) || $sub == null) {
                $prevType = gettype($sub);
            } else if ($prevType != gettype($sub)) {
                throw new \UnexpectedValueException("Mixing data types in an array is stupid.\n".var_export($values, true)." on line ".$this->currentLinenumber.".");
            }
        }

        // Remove empty Array values
        return array_filter($values);
    }

    private function parseString ($string)
    {
        return strtr($string, array(
            '\\0'  => "\0",
            '\\t'  => "\t",
            '\\n'  => "\n",
            '\\r'  => "\r",
            '\\"'  => '"',
            '\\\\' => '\\',
        ));
    }

    private function __clone() {}
}


/**
* A TOML parser for PHP
*/
class Parser2
{
	protected $raw;
	protected $doc = array();
	protected $group;
	protected $lineNum = 1;

	public function __construct($raw)
	{
		$this->raw = $raw;
		$this->group = &$this->doc;
	}

	static public function fromString($s)
	{
		$parser = new self($s);

		return $parser->parse();
	}

	static public function fromFile($path)
	{
		if(!is_file($path) || !is_readable($path)) {
			throw new \RuntimeException(sprintf('`%s` does not exist or cannot be read.', $path));
		}

		return self::fromString(file_get_contents($path));
	}

	public function parse()
	{
		$inString   = false;
		$arrayDepth = 0;
		$inComment  = false;
		$buffer     = '';

		// Loop over each character in the file, each line gets built up in $buffer
		// We can't simple explode on newlines because arrays can be declared
		// over multiple lines.
		for($i = 0; $i < strlen($this->raw); $i++) {
			$char = $this->raw[$i];

			// Detect start of comments
			if($char === '#' && !$inString) {
				$inComment = true;
			}

			// Detect start / end of string boundries
			if($char === '"' && $this->raw[$i-1] !== '\\') {
				$inString = !$inString;
			}

			if($char === '[' && !$inString) {
				$arrayDepth++;
			}

			if($char === ']' && !$inString) {
				$arrayDepth--;
			}

			// At a line break or the end of the document see whats going on
			if($char === "\n") {
				$this->lineNum++;
				$inComment = false;
				
				// Line breaks arent allowed inside strings
				if($inString) {
					throw new \Exception('Multiline strings are not supported.');	
				}

				if($arrayDepth === 0) {
					$this->processLine($buffer);
					$buffer = '';
					continue;
				}
			}

			// Don't append to the buffer if we're inside a comment
			if($inComment) {
				continue;
			}

			$buffer.= $char;
		}

		if($arrayDepth > 0) {
			throw new \Exception(sprintf('Unclosed array on line %s', $this->lineNum));
		}

		// Process any straggling content left in the buffer
		$this->processLine($buffer);

		return $this->doc;
	}

	protected function processLine($raw)
	{
		// replace new lines with a space to make parsing easier down the line.
		$line = str_replace("\n", ' ', $raw);
		$line = trim($line);
		
		// Skip blank lines
		if(empty($line)) {
			return;
		}

		// Check for groups
		if(preg_match('/^\[([^\]]+)\]$/', $line, $matches)) {
			$this->setGroup($matches[1]);
			return;
		}

		// Look for keys
		if(preg_match('/^(\S+)\s*=\s*(.+)/u', $line, $matches)) {
			$this->group[$matches[1]] = $this->parseValue($matches[2]);
			return;
		}

		throw new \Exception(sprintf('Invalid TOML syntax `%s` on line %s.', $raw, $this->lineNum));
	}

	protected function setGroup($keyGroup)
	{
		$parts = explode('.', $keyGroup);

		$this->group = &$this->doc;
		foreach($parts as $part) {
			if(!isset($this->group[$part])) {
				$this->group[$part] = array();
			} elseif(!is_array($this->group[$part])) {
				throw new \Exception(sprintf('%s has already been defined.', $keyGroup));
			}

			$this->group = &$this->group[$part];
		}
	}

	protected function parseValue($value)
	{
		// Detect bools
		if($value === 'true' || $value === 'false') {
			return $value === 'true';
		}

		// Detect floats
		if(preg_match('/^\-?\d+\.\d+$/', $value)) {
			return (float)$value;
		}

		// Detect integers
		if(preg_match('/^\-?\d*?$/', $value)) {
			return (int)$value;
		}

		// Detect string
		if(preg_match('/^"(.*)"$/u', $value, $matches)) {
			return $this->parseString($value);
		}
		
		// Detect datetime
		if(preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})Z$/', $value)) {
			return new \Datetime($value);
		}

		// Detect arrays
		if(preg_match('/^\[(.*)\]$/u', $value)) {
			return $this->parseArray($value);
		}
		
		throw new \Exception(sprintf('Unknown primative for `%s` on line %s.', $value, $this->lineNum));
	}

	protected function parseString($string)
	{
		$string = trim($string, '"');

		$allowedEscapes = array(
			'\\0'  => "\0",
			'\\t'  => "\t",
			'\\n'  => "\n",
			'\\r'  => "\r",
			'\\"'  => '"',
			'\\\\' => '\\',
		);

		// Check for invalid escape codes by removing valid ones and looking for backslash character
		// This negates any complex regex to detect two (or more) adjoining back slash escape sequences
		$check = str_replace(array_keys($allowedEscapes), '', $string);
		if(false !== strpos($check, '\\')) {
			throw new \Exception(sprintf('Invalid escape sequence on line %s', $this->lineNum));
		}

		return strtr($string, $allowedEscapes);
	}

	protected function parseArray($array)
	{
		// strips the outer wrapping [ and ] characters and and whitespace from the strip
		$array = preg_replace('/^\s*\[\s*(.*)\s*\]\s*$/usm', "$1", $array);

		$depth            = 0;
		$buffer           = '';
		$result           = array();
		$insideString     = false;
		$insideComment    = false;

		// TODO: This is a 80% duplicate of the logic in the parse() method.
		// Find a way to combine these blocks
		for($i = 0; $i < strlen($array); $i++) {
			
			if(!$insideString && $array[$i] === '[') {
				$depth++;
			}

			if(!$insideString && $array[$i] === ']') {
				$depth--;
			}

			if($array[$i] === '"' && ((isset($array[$i-1]) && $array[$i-1] !== '\\') || $i === 0))  {
				$insideString = !$insideString;
			}

			if(!$insideString && $array[$i] === '#') {
				$insideComment = true;
			}

			if(!$insideString && $array[$i] === ',' && 0 === $depth) {
				$result[] = $this->parseValue(trim($buffer));
				$this->validateArrayElementTypes($result);
				$buffer = '';
				continue;
			}

			if($array[$i] === "\n") {
				$insideComment = false;
			}

			if($insideComment === true) {
				continue;
			}

			$buffer.= $array[$i];
		}

		// Detect if array hasnt been closed properly
		if(0 !== $depth) {
			throw new \Exception(sprintf('Unclosed array on line %s', $this->lineNum));
		}

		// whatever meaningful text left in the buffer should be the last element
		if($buffer = trim($buffer)) {
			$result[] = $this->parseValue($buffer);
			$this->validateArrayElementTypes($result);
		}

		return $result;
	}

	protected function validateArrayElementTypes($array)
	{
		if(count($array) < 2) {
			return;
		}

		// Check the last two elements match in type (and classname if they are objects)
		// TODO: Tidy this up
		$indexA = count($array) - 2;
		$indexB = count($array) - 1;
		$typeA = gettype($array[$indexA]) === 'object' ? get_class($array[$indexA]) : gettype($array[$indexA]);
		$typeB = gettype($array[$indexB]) === 'object' ? get_class($array[$indexB]) : gettype($array[$indexB]);

		if($typeA !== $typeB) {
			throw new \Exception(sprintf('Arrays cannot contain mixed types on line %s', $this->lineNum));
		}
	}
}