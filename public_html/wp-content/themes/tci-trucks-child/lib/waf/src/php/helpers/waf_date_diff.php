<?php
function waf_date_diff( $date2, $date1='' ) {
    if( !$date1 ) $date1 = date( 'Y-m-d H:i:s', current_time('timestamp') );
    // Declare and define two dates 
    
    $time1 = strtotime( $date1 );
    $time2 = strtotime( $date2 );
    
    // Formulate the Difference between two dates 
    $diff = abs($time2 - $time1);  
    
    
    // To get the year divide the resultant date into 
    // total seconds in a year (365*60*60*24) 
    $years = floor($diff / (365*60*60*24));  
    
    
    // To get the month, subtract it with years and 
    // divide the resultant date into 
    // total seconds in a month (30*60*60*24) 
    $months = floor(($diff - $years * 365*60*60*24) 
                                / (30*60*60*24));  
    
    
    // To get the day, subtract it with years and  
    // months and divide the resultant date into 
    // total seconds in a days (60*60*24) 
    $days = floor(($diff - $years * 365*60*60*24 -  
                $months*30*60*60*24)/ (60*60*24)); 
    
    
    // To get the hour, subtract it with years,  
    // months & seconds and divide the resultant 
    // date into total seconds in a hours (60*60) 
    $hours = floor(($diff - $years * 365*60*60*24  
        - $months*30*60*60*24 - $days*60*60*24) 
                                    / (60*60));  
    
    
    // To get the minutes, subtract it with years, 
    // months, seconds and hours and divide the  
    // resultant date into total seconds i.e. 60 
    $minutes = floor(($diff - $years * 365*60*60*24  
            - $months*30*60*60*24 - $days*60*60*24  
                            - $hours*60*60)/ 60);  
    
    
    // To get the minutes, subtract it with years, 
    // months, seconds, hours and minutes  
    $seconds = floor(($diff - $years * 365*60*60*24  
            - $months*30*60*60*24 - $days*60*60*24 
                    - $hours*60*60 - $minutes*60));  
    return [$days,$hours,$minutes,$seconds];                   
}