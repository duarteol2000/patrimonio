<?php
/**
 * Tipoproduto Active Record
 * @author  <your-name-here>
 */
class Tipoproduto extends TRecord
{
    const TABLENAME = 'tipoproduto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $categoria;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('categoria_id');
    }

    
    /**
     * Method set_categoria
     * Sample of usage: $tipoproduto->categoria = $object;
     * @param $object Instance of Categoria
     */
    public function set_categoria(Categoria $object)
    {
        $this->categoria = $object;
        $this->categoria_id = $object->id;
    }
    
    /**
     * Method get_categoria
     * Sample of usage: $tipoproduto->categoria->attribute;
     * @returns Categoria instance
     */
    public function get_categoria()
    {
        // loads the associated object
        if (empty($this->categoria))
            $this->categoria = new Categoria($this->categoria_id);
    
        // returns the associated object
        return $this->categoria;
    }
    


}
