<?php
/**
 * Destino Active Record
 * @author  <your-name-here>
 */
class Destino extends TRecord
{
    const TABLENAME = 'destino';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
    }


}
