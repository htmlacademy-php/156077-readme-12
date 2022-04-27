<?php
    /**
     * Обрезает $text до $cropSybmols
     * @param [string] [$text] [текст для обрезки]
     * @param [number] [$cropSybmols] [до какого кол-ва символов обрезать]
     * @return {string} обрезанная строка со ссылкой Читать далее
     */
    
    function cropText($text, $cropSybmols = 300) {
        $words = explode(' ', $text);
        
        $symbolsCount = 0;
        $cropWords = [];

        $redMoreElement = '
            <div class="post-text__more-link-wrapper">
                <a class="post-text__more-link" href="#">Читать далее</a>
            </div>
        ';

        foreach ($words as $word) {
            $symbolsCount += strlen($word);   

            if ($symbolsCount > $cropSybmols) {            
                return implode(' ', $cropWords) . '...' . $redMoreElement; 
            } 
            
            array_push($cropWords, $word);
        }

        return $text;            
    }