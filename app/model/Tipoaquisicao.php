<?php
/**
 * Tipoaquisicao Active Record
 * @author  <your-name-here>
 */
class Tipoaquisicao extends TRecord
{
    const TABLENAME = 'tipoaquisicao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descaquisicao');
    }


}
