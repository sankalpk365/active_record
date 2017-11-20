<?php

class formhtml{
    
    public static function tableDisplayFunction($records){

    $html = '<table border=1><tbody>';
    $html .= '<tr>';
    foreach($records[0] as $key=>$value)
    {
    $html .= '<th>' . htmlspecialchars($key) . '</th>';
    }

    $html .= '</tr>';
    //$i = 0;
    foreach($records as $key=>$value)
    {
    $html .= '<tr>';
       
    foreach($value as $key2=>$value2)
    {
    $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
    }
    $html .= '</tr>';
					          
    //$i++;
    }
    $html .= '</tbody></table>';
    return $html;
}    

public static function tableDisplayFunction_1($records){
  
    $html = '<table border=1><tbody>';
    $html .= '<tr>';   
    foreach($records as $key => $value)
    {
    $html .= '<th>' . htmlspecialchars($key) . '</th>';
    }
    $html .= '</tr>';
            
    foreach($records as $value){
    $html .= '<td>' . $value . '</td>';
    }
    $html .='</tr></table>';
    return $html;
    }
}  
?>
														    
