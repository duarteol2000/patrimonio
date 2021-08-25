<?php
/**
 * Estadoprod Active Record
 * @author  <your-name-here>
 */
class Estadoprod extends TRecord
{
    const TABLENAME = 'estadoprod';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('estadoprod');
    }


}
