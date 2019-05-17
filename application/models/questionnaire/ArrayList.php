<?php

/**
 * ArrayList class
 *
 * @package Class
 * @version 0.3
 * @author Tim Anlauf <schranzistorradio@gmx.de>
 * @author Martin Kock <code @ deeagle.de>
 **/
class ArrayList
{

    /**
     * Array, representing the arrayList
     **/
    var $arrayList;

    /**
     * Pointer variable. used to detect the last element of the list in hasNext()
     * method.
     **/
    var $pointer = 0;

    /**
     * Constructor
     * Constructs a new list. The Parameter $arr is optional. If set an ArrayList
     * with the elements in array is created. Otherwise a empty list is
     * constructed.
     * @param arr - one dimensional array (optional)
     **/
    function __construct ( $arr = "" )
    {
        if( is_array( $arr ) == true )
            $this -> arrayList = $arr;
        else
            $this -> arrayList = array( );
    }

    /**
     * Inserts the specified element at the specified position in this list.
     * @param index integer - position in list
     * @param $obj
     **/
    function addToPos( $index, $obj )
    {
        if( $this -> isInteger( $index ) )
            $this -> arrayList[$index] = $obj;
        else
            die( "ERROR in ArrayList.addToPos <br> Integer value required" );
    }

    /**
     * Appends the specified element to the end of this list.
     * @param
     **/
    function add( $obj )
    {
        array_push( $this -> arrayList, $obj );
    }

    /**
     * Appends all of the elements in the specified Array to the end of this list
     * @param arr - one dimensional array
     **/
    function addAll( $arr )
    {
        $this -> arrayList = array_merge( $this -> arrayList, $arr );
    }

    /**
     * Removes all of the elements from this list.
     **/
    function clear( )
    {
        $this -> arrayList = array( );
    }

    /**
     * Returns true if this list contains the specified element.
     * @param obj
     * @return boolean
     **/
    function contains( $obj )
    {
        return in_array( $obj, $this -> arrayList );
    }

    /**
     *  Returns the element at the specified position in this list.
     * @param index
     **/
    function get( $index )
    {
        if( $this -> isInteger( $index ) )
            return $this -> arrayList[$index];
        else
            die( "ERROR in ArrayList.get <br> Integer value required" );
    }

    /**
     * Searches for the first occurence of the given argument. If the element
     * isn´t found, -1 is returned
     * @param obj
     * @return integer
     **/
    function indexOf( $obj )
    {
        while( list( $key, $val ) = each( $this -> arrayList ) )
            if( $obj == $val )
                return $key;
        return -1;
    }

    /**
     * Tests if this list has no elements.
     * @return boolean
     **/
    function isEmpty( )
    {
        if( count( $this -> arrayList ) == 0 )
            return true;
        else
            return false;
    }

    /**
     * Returns the index of the last occurrence of the specified object in this
     * list.
     * @param obj
     * return integer
     **/
    function lastIndexOf( $obj )
    {
        return array_search( $obj, $this -> arrayList );
    }

    /**
     * removes the element at the specified position in this list.
     * @param index
     **/
    function remove( $index )
    {
        if( $this -> isInteger( $index ) )
        {
            $newArrayList = array( );

            for( $i = 0; $i < $this -> size( ); $i++ )
                if( $index != $i )
                    $newArrayList[] = $this -> get( $i );

            $this -> arrayList = $newArrayList;
        }
        else
        {
            die( "ERROR in ArrayList.remove <br> Integer value required" );
        }
    }

    /**
     * Removes from this List all of the elements whose index is between
     * fromIndex, inclusive and toIndex, exclusive.
     * 
     * @param $fromIndex
     * @param $toIndex
     **/
    function removeRange( $fromIndex, $toIndex )
    {
        if( $this -> isInteger( $fromIndex ) && $this -> isInteger( $toIndex ) )
        {
            $newArrayList = array( );

            for( $i = 0; $i < $this -> size( ); $i++ )
                if( $i < $fromIndex || $i > $toIndex )
                    $newArrayList[] = $this -> get( $i );

            $this -> arrayList = $newArrayList;
        }
        else
            die( "ERROR in ArrayList.removeRange <br> Integer value required" );
    }

    /**
     * Returns the number of elements in this list.
     * return integer
     **/
    function size( )
    {
        return count( $this -> arrayList );
    }

    /**
     * Sorts the list in alphabetical order. Keys are not kept in position.
     **/
    function sort( )
    {
        sort( $this -> arrayList );
    }

    /**
     * Returns an array containing all of the elements in this list in the
     * correct order.
     * @return array
     **/
    function toArray( )
    {
        return $this -> arrayList;
    }

    /* Iterator Methods */

    /**
     * Returns true if the list has more elements. Advice : excecute reset method
     * before
     * using this method
     * @return boolean
     **/
    function hasNext( )
    {
        $this -> pointer++;

        if( $this -> pointer == $this -> size( ) )
            return false;
        else
            return true;
    }

    /**
     * Set the pointer of the list to the first element
     **/
    function reset( )
    {
        reset( $this -> arrayList );
        $this -> pointer = 0;
    }

    /**
     * Set the pointer of the next element of the list
     * @return current element
     **/
    function next( )
    {
        $cur = current( $this -> arrayList );
        next( $this -> arrayList );
        return $cur;
    }

    /* private Methods */

    /**
     * Returns true if the parameter holds an integer value
     * @param int
     * @return boolean
     **/
    function isInteger( $toCheck )
    {
        // return eregi( "^-?[0-9]+$", $toCheck );
        return is_int( $toCheck );
    }

}
?>