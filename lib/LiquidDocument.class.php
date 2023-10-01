<?php

class LiquidDocument extends LiquidBlock
{

    /**
     * Constructor
     *
     * @param array $tokens
     * @param LiquidFileSystem $fileSystem
     * @return LiquidDocument
     */
    function __construct($tokens, &$fileSystem)
    {
        $this->_fileSystem = $fileSystem;
        $this->parse($tokens);
    }


    /**
     * check for cached includes
     */
    public function checkIncludes(): bool|string
    {
        $return = false;
        foreach ($this->_nodelist as $token) {
            if (is_object($token)) {
                if (get_class($token) == 'LiquidTagInclude' || get_class($token) == 'LiquidTagExtends') {
                    if ($token->checkIncludes() == true) {
                        $return = true;
                    }
                }
            }
        }
        return $return;
    }


    /**
     * There isn't a real delimiter
     */
    function blockDelimiter(): string
    {
        return '';
    }


    /**
     * Document blocks don't need to be terminated since they are not actually opened
     *
     */
    function assertMissingDelimitation()
    {
    }
}
