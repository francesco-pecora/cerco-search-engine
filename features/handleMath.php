<?php

/**
 * evaluateMathEquation check if the search input is an equation, calculates the result and returns it
 * 
 * @param $term -> the search input
 * @return -> the result if the search input is a math equation; false if not.
 */
function evaluateMathEquation($term) {
    if(preg_match('/(\d+)(?:\s*)([\+\-\*\/])(?:\s*)(\d+)/', $term, $matches) !== 0){
        $operator = $matches[2];
    
        switch($operator){
            case '+':
                $result = $matches[1] + $matches[3];
                break;
            case '-':
                $result = $matches[1] - $matches[3];
                break;
            case '*':
                $result = $matches[1] * $matches[3];
                break;
            case '/':
                $result = $matches[1] / $matches[3];
                break;
        }
    
        return $result;
    }
    return false;
}
?>