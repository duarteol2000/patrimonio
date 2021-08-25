<?php
/**
 * Empresa Active Record
 * @author  <your-name-here>
 */
class Empresa extends TRecord
{
    const TABLENAME = 'empresa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cpf_cnpj');
        parent::addAttribute('nome');
        parent::addAttribute('nomefantasia');
        parent::addAttribute('contato1');
        parent::addAttribute('contato2');
        parent::addAttribute('cep');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('estado');
        parent::addAttribute('cidade');
    }


}
