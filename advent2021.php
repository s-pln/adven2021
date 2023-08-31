<?php 
class Advent2021 
{
    // property declaration
    public $input;
    
    // constructor
    function __construct($file) {
        $this->input = file_get_contents($file);
    }
    
    // --- Day 1: Sonar Sweep ---

    public function countIncreasedMeasurements(){ // --- Part One ---
        $measurements = $this->linesToArray();

        $increased_count = 0; // count the number of times a depth measurement increases from the previous measurement

        foreach ($measurements as $n => $measurement) {
            if ($n != 0 && intval($measurement, 10) > $last_measurement) {
                $increased_count++;
            }
            $last_measurement = intval($measurement, 10);
        }
        echo '<h1>ADVENT 2021</h1><h2>Day 1: Sonar Sweep</h2><strong>Part One:</strong><br>Result: ';
        echo $increased_count;
    }

    public function countIncreasedThreeMeasurements(){ // --- Part Two ---
        $measurements = $this->linesToArray();

        $increased_count = 0; // count the number of times the sum of measurements in this sliding window increases from the previous sum 

        $sum_A = 0;
        $sum_B = 0;
        $sum_C = 0;
        $sum_D = 0;

        // 0%4 = 0    199  A      
        // 1%4 = 1    200  A B    
        // 2%4 = 2    208  A B C  
        // 3%4 = 3    210    B C D
        // 4%4 = 0    200  A   C D
        // 5%4 = 1    207  A B   D
        // 6%4 = 2    240  A B C  
        // 7%4 = 3    269    B C D
        // 8%4 = 0    260  A   C D
        // 9%4 = 1    263  A B   D
        // ...

        foreach ($measurements as $n => $measurement) {
            $int_measurement = intval($measurement, 10);
            
            $mod = $n%4;
            switch ($mod) {
                case 0:
                    $sum_A = $int_measurement;

                    if ($n > 3) { 
                        $sum_C = $sum_C + $int_measurement; 
                        $sum_D = $sum_D + $int_measurement;
                        $increased_count = $this->compareAndCount($sum_C,$sum_B,$increased_count);
                    }
                    break;
                case 1:
                    $sum_A = $sum_A + $int_measurement;
                    $sum_B = $int_measurement;

                    if ($n > 3) { 
                        $sum_D = $sum_D + $int_measurement;
                        $increased_count = $this->compareAndCount($sum_D,$sum_C,$increased_count);
                    }
                    break;
                case 2:
                    $sum_A = $sum_A + $int_measurement;
                    $sum_B = $sum_B + $int_measurement;
                    $sum_C = $int_measurement;

                    if ($n > 3) { 
                        $increased_count = $this->compareAndCount($sum_A,$sum_D,$increased_count);
                    }
                    break;
                case 3:
                    $sum_B = $sum_B + $int_measurement;
                    $sum_C = $sum_C + $int_measurement;
                    $sum_D = $int_measurement;

                    if ($n > 3) { 
                        $increased_count = $this->compareAndCount($sum_B,$sum_A,$increased_count);
                    }
                    break;
            }
        }
        echo '<br><br><strong>Part Two:</strong><br>Result: ';
        echo $increased_count;
    }

    private function linesToArray(){
        return explode("\n", $this->input); // each line is one item
    } 

    private function compareAndCount($sum1,$sum2,$count){ // If sum is larger than the previous sum, increase count
        if ($sum1 > $sum2) {
            return $count + 1;
        }else{
            return $count;
        }
    }


    // --- Day 2: Dive! ---
    public function getFinalPosition(){ // --- Part One ---
        $commands = $this->linesToArray();
        // print_r( $commands);

        $position = [];
        $position['horizontal']  = 0;
        $position['depth'] = 0;

        foreach ($commands as $n => $command) {
            
            if (str_contains($command, 'forward')) {
                $position['horizontal']  = $position['horizontal']  + intval(str_replace('forward ','',$command), 10); // forward X increases the horizontal position by X units.
            } else if (str_contains($command, 'down')) {
                $position['depth'] = $position['depth'] + intval(str_replace('down ','',$command), 10); // down X increases the depth by X units.
            } else {
                $position['depth'] = $position['depth'] - intval(str_replace('up ','',$command), 10); // up X decreases the depth by X units.
            }
        }
        echo '<h1>ADVENT 2021</h1><h2>Day 2: Dive!</h2><strong>Part One:</strong><br>';
        echo 'horizontal position: '.$position['horizontal'].'<br>';
        echo 'depth: '.$position['depth'].'<br>';
        echo 'horizontal position x depth: '.$position['horizontal']*$position['depth'];

        return $position;
    }

    public function getFinalAimPosition(){ // --- Part Two ---
        $commands = $this->linesToArray();
        // print_r( $commands);

        $position = [];
        $position['horizontal']  = 0;
        $position['depth'] = 0;
        $position['aim'] = 0;

        foreach ($commands as $n => $command) {

            if (str_contains($command, 'forward')) {
                $val = intval(str_replace('forward ','',$command), 10);  // forward X does two things:
                                            
                $position['horizontal'] = $position['horizontal'] + $val;        //     It increases your horizontal position by X units.
                $position['depth'] = $position['depth'] + $position['aim'] * $val;       //     It increases your depth by your aim multiplied by X.
            } else if (str_contains($command, 'down')) {                   
                $position['aim'] = $position['aim'] + intval(str_replace('down ','',$command), 10);   // down X increases your aim by X units.
            } else {                                                      
                $position['aim'] = $position['aim'] - intval(str_replace('up ','',$command), 10);   // up X decreases your aim by X units.
            }
        }

        echo '<br><br><strong>Part Two:</strong><br>';
        echo 'horizontal position: '.$position['horizontal'].'<br>';
        echo 'depth: '.$position['depth'].'<br>';
        echo 'aim: '.$position['aim'].'<br>';
        echo 'horizontal position x depth: '.$position['horizontal']*$position['depth'];

        return $position;
    }

    // --- Day 3: Binary Diagnostic ---
    public function getPowerConsumption(){ // --- Part One ---

        $report_nums = $this->linesToArray();

        $gamma = 0; // gamma rate
        $epsilon = 0; // epsilon rate

        $counter_of_1 = [];

        foreach ($report_nums as $n => $num) {  // count num '1' on each position
        // echo $num.'<br>';
            $num_array = str_split($num);  

            foreach ($num_array as $key => $value) {
                if($value == '1' || $value == '0'){
                    if(!array_key_exists($key, $counter_of_1)){
                    $counter_of_1[$key] = 0;
                    }else{
                        if($value == '1'){
                            $counter_of_1[$key]++;
                        }
                    }
                }
                
            }

        }

        $min = sizeof($report_nums) / 2; //  half of size - most common bit have to be more than this
        $gamma_array = [];
        $epsilon_array = [];
        foreach ($counter_of_1 as $n => $val) {
            if($val > $min){ 
                $gamma_array[$n] = 1; 
                $epsilon_array[$n] = 0; 
            }else{ 
                $gamma_array[$n] = 0; 
                $epsilon_array[$n] = 1; 
            }
        }
        $gamma = implode('',$gamma_array );
        $epsilon = implode('',$epsilon_array );

        echo '<h1>ADVENT 2021</h1><h2>Day 3: Binary Diagnostic</h2><strong>Part One:</strong><br>';
        echo 'gamma rate: '.$gamma.'<br>';
        echo 'epsilon rate: '.$epsilon.'<br><br>';
        echo 'gamma rate: '.intval($gamma,2) .'<br>';
        echo 'epsilon rate: '.intval($epsilon,2).'<br><br>';
        $power_consumption = intval($gamma,2) * intval($epsilon,2);
        echo 'power consumption: '.$power_consumption.'<br><br>';

        return $power_consumption;

    }
}

?>