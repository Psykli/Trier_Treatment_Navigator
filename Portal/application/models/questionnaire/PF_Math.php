<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * An instance of the library includes all math helpers for
 * questionnaire process.
 *
 * @package Questionnaire
 * @category Library
 *
 * @author Martin Kock <code @ deeagle.de>
 */
class PF_Math
{
    /**
     * Statistical error-value -9999.
     */
    private $bow_error = -9999;

    /**
     * Constructor.
     */
    public function __construct( )
    {
    }//__construct()

    /**
     * Calculates: diff = minuend - subtrhaend
     *
     * @since 0.3.0
     * @access public
     * @param int $minuend
     * @param int subtrahend
     * @return int differenz, otherwise NULL.
     */
    public function diff( $minuend, $subtrahend )
    {
        $diff = NULL;

        //All vars setted?
        if( !isset( $minuend ) OR !isset( $subtrahend ) )
        {
            //log_message( 'error', "try diff but no data given" );
        }//if
        else
        {
            //All vars aren't numeric?
            if( !is_numeric( $minuend ) OR !is_numeric( $subtrahend ) )
            {
                //log_message( 'error', 'Some arguements are not numeric.' );
            }//if
            else
            {
                //subtrahend isn't bow error?
                if( $subtrahend != $this -> bow_error )
                {
                    $diff = $minuend - $subtrahend;
                }//if
                else
                {
                    //log_message( 'error', 'Subtrahend is bow-error.' );
                }//else
            }//else
        }//else

        return $diff;
    }//diff()

    /**
     * Calculates the mean of some values.
     *
     * @since 0.3.0
     * @access public
     * @param array[int] questionnaire values
     * @return int the mean of the values, ohterwise NULL.
     *
     * @todo ask for a description
     */
    public function mean( $bow_values )
    {
        $result = NULL;

        $sum = 0;
        $n = 0;
        $m = 0;

        if( !isset( $bow_values ) )
        {
            log_message( 'error', "try mean but no data given." );
        }//if
        else
        {
            foreach( $bow_values as $value )
            {
                if( !is_null( $value ) AND is_numeric( $value ) AND ($value != $this -> bow_error) )
                {
                    $sum += $value;
                    $n++;
                }//if
                $m++;
            }//foreach()

            if( ($n > 0) AND ($n / $m >= 0.80) )
            {
                $result = $sum / $n;
            }//if
        }//else

        return $result;
    }//mean()
	
	public function calc_sum( $bow_values )
    {
		$result = NULL;
		
        $sum = 0;

        if( !isset( $bow_values ) )
        {
            log_message( 'error', "try mean but no data given." );
        }//if
        else
        {
            foreach( $bow_values as $value )
            {
                if( !is_null( $value ) AND is_numeric( $value ) AND ($value != $this -> bow_error) )
                {
                    $sum += $value;
                    $n++;
                }//if
                $m++;
            }//foreach()

            if( ($n > 0) AND ($n / $m >= 0.80) )
            {
                $result = $sum;
            }//if
        }//else

        return $result;
    }//mean()

    /**
     * Calculates the mean of some values.
     *
     * @since 0.3.0
     * @access public
     * @param array[int] questionnaire values
     * @return int the mean of the values, ohterwise NULL.
     *
     * @todo ask for a description
     */
    public function mean2( $bow_values )
    {
        $result = NULL;

        $sum = 0;
        $n = 0;
        $m = 0;

        if( !isset( $bow_values ) )
        {
            log_message( 'error', "try mean but no data given." );
        }//if
        else
        {
            foreach( $bow_values as $value )
            {
                if( (!is_null( $value[0] )) AND is_numeric( $value[0] ) AND ($value[0] != $this -> bow_error) )
                {
                    if( $value[1] == 0 )
                    {
                        $sum += $value[0];
                    }//if
                    else
                    {
                        $sum += $value[1] - $value[0];
                    }//else
                    $n++;
                }//if
                $m++;
            }//foreach()

            if( ($n > 0) AND ($n / $m >= 0.80) )
            {
                return $sum / $n;
            }//if
        }//else

        return $result;
    }//mean2()

    /**
     * Calculates the t value of some values.
     *
     * @since
     * @access public
     * @param number $raw
     * @param number $mean
     * @param number $stddev
     * @return numeric , otherwise NULL.
     *
     * @todo ask for a description
     */
    public function twert( $raw, $mean, $stddev )
    {
        $twert = NULL;

        // All vars setted?
        if( !isset( $raw ) OR !isset( $mean ) OR !isset( $stddev ) )
        {
            log_message( 'error', "try twert but no data given." );
        }//if
        else
        {
            //All vars are numeric?
            if( !is_numeric( $raw ) OR !is_numeric( $mean ) OR !is_numeric( $stddev ) )
            {
                log_message( 'error', 'try twert but some data is not numeric.' );
            }//if
            else
            {
                //is stddev not zero?
                if( $stddev != 0 )
                {
                    $twert = ((($raw - $mean) / $stddev) * 10) + 50;
                }//if
                else
                {
                    log_message( 'error', 'Try twert, but Division by zero!' );
                }//else
            }//else
        }//else

        return $twert;
    }//twert()

    /**
     * Calculates the raw value of some values.
     *
     * @since
     * @access public
     * @param number $tvalue
     * @param number $mean
     * @param number $stddev
     * @return numeric , otherwise NULL.
     *
     * @todo ask for a description
     */
    public function twert_r( $tvalue, $mean, $stddev )
    {
        $raw = NULL;

        //All vars setted?
        if( !isset( $tvalue ) OR !isset( $mean ) OR !isset( $stddev ) )
        {
            log_message( 'error', 'try twert_r but no data given.' );
        }//if
        else
        {
            //All vars are numeric?
            if( !is_numeric( $tvalue ) OR !is_numeric( $mean ) OR !is_numeric( $stddev ) )
            {
                log_message( 'error', 'try twert_r but some data is not numeric.' );
            }//if
            else
            {
                $raw = ((($tvalue - 50) / 10) * $stddev) + $mean;
            }//else
        }//else

        return $raw;
    }//twert_r()

    /**
     * Calculates the z value of some values.
     *
     * @since
     * @access public
     * @param number $raw
     * @param number $mean
     * @param number $stddev
     * @return numeric , otherwise NULL.
     *
     * @todo ask for a description
     */
    public function zwert( $raw, $mean, $stddev )
    {
        $zwert = NULL;

        // All vars setted?
        if( !isset( $raw ) OR !isset( $mean ) OR !isset( $stddev ) )
        {
            log_message( 'error', "try zwert but no data given." );
        }//if
        else
        {
            //All vars are numeric?
            if( !is_numeric( $raw ) OR !is_numeric( $mean ) OR !is_numeric( $stddev ) )
            {
                log_message( 'error', 'try zwert but some data is not numeric.' );
            }//if
            else
            {
                //is stddev not zero?
                if( $stddev != 0 )
                {
                    $zwert = ($raw - $mean) / $stddev;
                }//if
                else
                {
                    log_message( 'error', 'Try zwert, but Division by zero!' );
                }//else
            }//else
        }//else

        return $zwert;
    }//zwert()

    /**
     * Calculates the raw value of some values.
     *
     * @since
     * @access public
     * @param number $zvalue
     * @param number $mean
     * @param number $stddev
     * @return numeric , otherwise NULL.
     *
     * @todo ask for a description
     */
    public function zwert_r( $zvalue, $mean, $stddev )
    {
        $raw = NULL;

        //All vars setted?
        if( !isset( $zvalue ) OR !isset( $mean ) OR !isset( $stddev ) )
        {
            log_message( 'error', 'try twert_r but no data given.' );
        }//if
        else
        {
            //All vars are numeric?
            if( !is_numeric( $zvalue ) OR !is_numeric( $mean ) OR !is_numeric( $stddev ) )
            {
                log_message( 'error', 'try twert_r but some data is not numeric.' );
            }//if
            else
            {
                $raw = ($zvalue * $stddev) + $mean;
            }//else
        }//else

        return $raw;
    }//zwert_r()

    /**
     * Calculates the pr_a of some values.
     * 
     * @since
     * @access public
     * @param number $raw
     * @param number $mean
     * @param number $stddev
     * @return number , otherwise NULL.
     * 
     * @todo ask for a description
     */
    public function pr_a( $raw, $mean, $stddev )
    {
        $result = NULL;
        
        // All vars setted?
        if( !isset( $raw ) OR !isset( $mean ) OR !isset( $stddev ) )
        {
            log_message( 'error', "try pr_a but no data given." );
        }//if
        else
        {
            //All vars are numeric?
            if( !is_numeric( $raw ) OR !is_numeric( $mean ) OR !is_numeric( $stddev ) )
            {
                log_message( 'error', 'try pr_a but some data is not numeric.' );
            }//if
            else
            {
                //is stddev not zero?
                if( $stddev != 0 )
                {
                    $z = ( $raw - $mean ) / $stddev;
                    
                    if( $z < 0 )
                    {
                        $zb = 0 - $z;
                    }//if
                    else
                    {
                        $zb = $z;
                    }//else
                        
                    switch( $zb )
                    {
                        case ( $zb < 0.025 ): $prb = 0.50; break;
                        case ( $zb < 0.050 ): $prb = 0.51; break;
                        case ( $zb < 0.075 ): $prb = 0.52; break;
                        case ( $zb < 0.100 ): $prb = 0.53; break;
                        case ( $zb < 0.126 ): $prb = 0.54; break;
                        case ( $zb < 0.151 ): $prb = 0.55; break;
                        case ( $zb < 0.176 ): $prb = 0.56; break;
                        case ( $zb < 0.202 ): $prb = 0.57; break;
                        case ( $zb < 0.228 ): $prb = 0.58; break;
                        case ( $zb < 0.253 ): $prb = 0.59; break;
                        case ( $zb < 0.279 ): $prb = 0.60; break;
                        case ( $zb < 0.305 ): $prb = 0.61; break;
                        case ( $zb < 0.332 ): $prb = 0.62; break;
                        case ( $zb < 0.358 ): $prb = 0.63; break;
                        case ( $zb < 0.385 ): $prb = 0.64; break;
                        case ( $zb < 0.412 ): $prb = 0.65; break;
                        case ( $zb < 0.440 ): $prb = 0.66; break;
                        case ( $zb < 0.468 ): $prb = 0.67; break;
                        case ( $zb < 0.496 ): $prb = 0.68; break;
                        case ( $zb < 0.524 ): $prb = 0.69; break;
                        case ( $zb < 0.553 ): $prb = 0.70; break;
                        case ( $zb < 0.583 ): $prb = 0.71; break;
                        case ( $zb < 0.613 ): $prb = 0.72; break;
                        case ( $zb < 0.643 ): $prb = 0.73; break;
                        case ( $zb < 0.674 ): $prb = 0.74; break;
                        case ( $zb < 0.706 ): $prb = 0.75; break;
                        case ( $zb < 0.739 ): $prb = 0.76; break;
                        case ( $zb < 0.772 ): $prb = 0.77; break;
                        case ( $zb < 0.806 ): $prb = 0.78; break;
                        case ( $zb < 0.842 ): $prb = 0.79; break;
                        case ( $zb < 0.878 ): $prb = 0.80; break;
                        case ( $zb < 0.915 ): $prb = 0.81; break;
                        case ( $zb < 0.954 ): $prb = 0.82; break;
                        case ( $zb < 0.994 ): $prb = 0.83; break;
                        case ( $zb < 1.036 ): $prb = 0.84; break;
                        case ( $zb < 1.080 ): $prb = 0.85; break;
                        case ( $zb < 1.126 ): $prb = 0.86; break;
                        case ( $zb < 1.175 ): $prb = 0.87; break;
                        case ( $zb < 1.227 ): $prb = 0.88; break;
                        case ( $zb < 1.282 ): $prb = 0.89; break;
                        case ( $zb < 1.341 ): $prb = 0.90; break;
                        case ( $zb < 1.405 ): $prb = 0.91; break;
                        case ( $zb < 1.476 ): $prb = 0.92; break;
                        case ( $zb < 1.555 ): $prb = 0.93; break;
                        case ( $zb < 1.645 ): $prb = 0.94; break;
                        case ( $zb < 1.751 ): $prb = 0.95; break;
                        case ( $zb < 1.881 ): $prb = 0.96; break;
                        case ( $zb < 2.054 ): $prb = 0.97; break;
                        case ( $zb < 2.326 ): $prb = 0.98; break;
                        default:              $prb = 0.99;
                    }//switch
                    
                    if( $z < 0 )
                    {
                        $pr = 1 - $prb;
                    }//if
                    else
                    {
                        $pr = $prb;
                    }//else
                        
                    $result = $pr * 100;
                }//if
                else
                {
                    log_message( 'error', 'Try pr_a, but Division by zero!' );
                }//else
            }//else
        }//else
        
        return $result;
    }//pr_a()
    
    /**
     * Calculates the pr_b of some values.
     * 
     * @since
     * @access public
     * @param number $raw
     * @param number $mean
     * @param number $stddev
     * @return number , otherwise NULL.
     * 
     * @todo ask for a description
     */
     public function pr_b( $raw, $mean, $stddev )
    {
        $p  =  0.2316419;
        $c1 =  0.31938153;
        $c2 = -0.356563782;
        $c3 =  1.78147937;
        $c4 = -1.821255978;
        $c5 =  1.330274429;
        
        $result = NULL;
        
        // All vars setted?
        if( !isset( $raw ) OR !isset( $mean ) OR !isset( $stddev ) )
        {
            log_message( 'error', "try pr_b but no data given." );
        }//if
        else
        {
            //All vars are numeric?
            if( !is_numeric( $raw ) OR !is_numeric( $mean ) OR !is_numeric( $stddev ) )
            {
                log_message( 'error', 'try pr_b but some data is not numeric.' );
            }//if
            else
            {
                //is stddev not zero?
                if( $stddev != 0 )
                {
                    $z = ( $raw - $mean) / $stddev;
                    if( $z < 0 )
                    {
                        $zb = 0 - $z;
                    }//if
                    else
                    {
                        $zb = $z;
                    }//else
                    
                    $invsqrtpix2 = 1 / sqrt( 6.283185307 );

                    //need @line 488: $t = 1 / (1 + $p * $zb)
                    $tdiv = 1 + ( $p * $zb );
                    
                    //is $tdiv not zero?
                    if( $tdiv != 0 )
                    {
                        $t = 1 / $tdiv;
                        $dum = ( $c1 * $t )
                             + ( $c2 * pow( $t, 2 ) ) 
                             + ( $c3 * pow( $t, 3 ) ) 
                             + ( $c4 * pow( $t, 4 ) ) 
                             + ( $c5 * pow( $t, 5 ) );
                        $phib = ( $invsqrtpix2 * exp( 0 - ( $zb * $zb ) / 2 ) ) * $dum;
                        
                        if( $z < 0 )
                        {
                            $phi = $phib;
                        }//if
                        else
                        {
                            $phi = 1-$phib;
                        }//else
                        
                        $result = round( $phi * 100, 2 );
                    }//if
                    else
                    {
                        log_message( 'error', 'Try pr_b, but Division by zero (tdiv)!' );
                    }//else
                }//if
                else
                {
                    log_message( 'error', 'Try pr_b, but Division by zero (stddev)!' );
                }//else
            }//else
        }//else
            
        return $result;
    }//pr_b()

    /**
     * Calculates the pr_c of some values.
     * 
     * @since
     * @access public
     * @param number $raw
     * @param number $mean
     * @param number $stddev
     * @return number , otherwise NULL.
     * 
     * @todo ask for a description
     */
     public function pr_c( $raw)
    {
        $p  =  0.2316419;
        $c1 =  0.31938153;
        $c2 = -0.356563782;
        $c3 =  1.78147937;
        $c4 = -1.821255978;
        $c5 =  1.330274429;
        
        $result = NULL;
        
        // All vars setted?
        if( !isset( $raw ) )
        {
            log_message( 'error', "try pr_b but no data given." );
        }//if
        else
        {
            //All vars are numeric?
            if( !is_numeric( $raw ) )
            {
                log_message( 'error', 'try pr_b but some data is not numeric.' );
            }//if
            else
            {
                $z = $raw;
                if( $z < 0 )
                {
                    $zb = 0 - $z;
                }//if
                else
                {
                    $zb = $z;
                }//else
                
                $invsqrtpix2 = 1 / sqrt( 6.283185307 );

                //need @line 488: $t = 1 / (1 + $p * $zb)
                $tdiv = 1 + ( $p * $zb );
                
                //is $tdiv not zero?
                if( $tdiv != 0 )
                {
                    $t = 1 / $tdiv;
                    $dum = ( $c1 * $t )
                            + ( $c2 * pow( $t, 2 ) ) 
                            + ( $c3 * pow( $t, 3 ) ) 
                            + ( $c4 * pow( $t, 4 ) ) 
                            + ( $c5 * pow( $t, 5 ) );
                    $phib = ( $invsqrtpix2 * exp( 0 - ( $zb * $zb ) / 2 ) ) * $dum;
                    
                    if( $z < 0 )
                    {
                        $phi = $phib;
                    }//if
                    else
                    {
                        $phi = 1-$phib;
                    }//else
                    
                    $result = round( $phi * 100, 2 );
                }//if
                else
                {
                    log_message( 'error', 'Try pr_b, but Division by zero (tdiv)!' );
                }//else
            }//else
        }//else
            
        return $result;
    }//pr_b()
     
    /**
     * Caclculates the hscl value to given scl.
     * 
     * @since
     * @access public
     * @param number $scl
     * @return number , ohterwise NULL.
     * 
     * @todo ask for description
     */
    public function scl_to_hscl( $scl )
    {
        $hscl = NULL;
        
        //Var is setted?
        if( !isset( $scl ) )
        {
            log_message( 'error', 'try scl_to_hscl but no data given.' );
        }//if
        else
        {
            //Var is numeric?
            if( !is_numeric( $scl ) )
            {
                glog_message( 'error', 'try scl_to_hscl but data is not numeric.' );
            }//if
            else
            {
                //Var is not bow error?
                if( $scl != $this->bow_error )
                {
                    $hscl = $scl + 1;
                    if( $scl == 5 )
                    {
                        $hscl = 4;
                    }//if
                }//if
            }//else
        }//else
        
        return $hscl;
    }//scl_to_hscl()
	
	/**
     * Caclculates the hscl value to given bsi.
     * 
     * @since
     * @access public
     * @param number $bsi
     * @return number , ohterwise NULL.
     * 
     * @todo ask for description
     */
    public function bsi_to_hscl( $bsi )
    {      
        //Var is setted?
        if( !isset( $bsi ) )
        {
            log_message( 'error', 'try bsi_to_hscl but no data given.' );
        }//if
        else
        {
            //Var is numeric?
            if( !is_numeric( $bsi ) )
            {
                glog_message( 'error', 'try bsi_to_hscl but data is not numeric.' );
            }//if
            else
            {
                //Var is not bow error?
                if( $bsi != $this->bow_error )
                {
                    switch($bsi){
                        case 0:
                            $bsi = 1;
                            break;
                        case 1:
                            $bsi = 1.75;
                            break;
                        case 2:
                            $bsi = 2.5;
                            break;
                        case 3:
                            $bsi = 3.25;
                            break;
                        case 4:
                            $bsi = 4;
                            break;
                    }
                }//if
            }//else
        }//else
        
        return $bsi;
    }//scl_to_hscl()

	/**
	*
	* Codierungen (FEV) 
	*
	* @since
	* @access public
	* 1.) 2->1;1->0
	* 2.) 1->1;2->0
	* 3.) 1/2/3->0;4/5/6->1
	* 4.) 1/2/7->0;3/4/5/6->1
	* 5.) 1/2->1;3/4->0
	* 6.) 1/2->0;3/4->1
	*/
	public function cod( $cod=0, $param ) 
	{
		if ($cod==0){return $param;}
		if ($cod==1){ if($param == 1){return 0;} elseif($param == 2){return 1;}}
		if ($cod==2){ if($param == 1){return 1;} elseif($param == 2){return 0;}}
		
		if ($cod==3)
		{ 
			if($param == 1 || $param == 2 || $param == 3 )
				return 0;
			elseif($param == 4 || $param == 5 || $param == 6 )
				return 1;
		}
		
		if ($cod==4)
		{
			if($param == 1 || $param == 2 || $param == 7 )
				return 0;
			elseif($param == 3 || $param == 4 || $param == 5 || $param == 6 )
				return 1;
		}
		
		if( $cod==5 )
		{
			if($param == 1 || $param == 2)
				return 1;
			elseif($param == 3 || $param == 4)
				return 0;
		}
		
		if ($cod==6)
		{
			if($param == 1 || $param == 2)
				return 0;
			elseif($param == 3 || $param == 4)
				return 1;
		}
		
		if ($cod==7){ if($param == 1 || $param == 5){return 6;} elseif($param == 2 || $param == 4){return 3;} elseif($param == 3){return 1;}}
		if ($cod==8){ if($param == 0) {return 0;} elseif($param == 1 || $param == 2 || $param == 3 || $param == 4){return 1;}}
	}//cod()
}//class PF_Math()
?>