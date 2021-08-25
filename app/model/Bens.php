<?php
/**
 * Bens Active Record
 * @author  <your-name-here>
 */
class Bens extends TRecord
{
    const TABLENAME = 'bens';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $tipotomb;
    private $estadoprod;
    private $empresa;
    private $tipoproduto;
    private $tipoaquisicao;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('num_tombamento');
        parent::addAttribute('descricao');
        parent::addAttribute('empresa_id');
        parent::addAttribute('data_receb');
        parent::addAttribute('tipoaquisicao_id');
        parent::addAttribute('estadoprod_id');
        parent::addAttribute('numdoc');
        parent::addAttribute('num_notafiscal');
        parent::addAttribute('valordoben');
        parent::addAttribute('tipotomb_id');
        parent::addAttribute('tipoproduto_id');
    }

    
    /**
     * Method set_tipotomb
     * Sample of usage: $bens->tipotomb = $object;
     * @param $object Instance of Tipotomb
     */
    public function set_tipotomb(Tipotomb $object)
    {
        $this->tipotomb = $object;
        $this->tipotomb_id = $object->id;
    }
    
    /**
     * Method get_tipotomb
     * Sample of usage: $bens->tipotomb->attribute;
     * @returns Tipotomb instance
     */
    public function get_tipotomb()
    {
        // loads the associated object
        if (empty($this->tipotomb))
            $this->tipotomb = new Tipotomb($this->tipotomb_id);
    
        // returns the associated object
        return $this->tipotomb;
    }
    
    
    /**
     * Method set_estadoprod
     * Sample of usage: $bens->estadoprod = $object;
     * @param $object Instance of Estadoprod
     */
    public function set_estadoprod(Estadoprod $object)
    {
        $this->estadoprod = $object;
        $this->estadoprod_id = $object->id;
    }
    
    /**
     * Method get_estadoprod
     * Sample of usage: $bens->estadoprod->attribute;
     * @returns Estadoprod instance
     */
    public function get_estadoprod()
    {
        // loads the associated object
        if (empty($this->estadoprod))
            $this->estadoprod = new Estadoprod($this->estadoprod_id);
    
        // returns the associated object
        return $this->estadoprod;
    }
    
    
    /**
     * Method set_empresa
     * Sample of usage: $bens->empresa = $object;
     * @param $object Instance of Empresa
     */
    public function set_empresa(Empresa $object)
    {
        $this->empresa = $object;
        $this->empresa_id = $object->id;
    }
    
    /**
     * Method get_empresa
     * Sample of usage: $bens->empresa->attribute;
     * @returns Empresa instance
     */
    public function get_empresa()
    {
        // loads the associated object
        if (empty($this->empresa))
            $this->empresa = new Empresa($this->empresa_id);
    
        // returns the associated object
        return $this->empresa;
    }
    
    
    /**
     * Method set_tipoproduto
     * Sample of usage: $bens->tipoproduto = $object;
     * @param $object Instance of Tipoproduto
     */
    public function set_tipoproduto(Tipoproduto $object)
    {
        $this->tipoproduto = $object;
        $this->tipoproduto_id = $object->id;
    }
    
    /**
     * Method get_tipoproduto
     * Sample of usage: $bens->tipoproduto->attribute;
     * @returns Tipoproduto instance
     */
    public function get_tipoproduto()
    {
        // loads the associated object
        if (empty($this->tipoproduto))
            $this->tipoproduto = new Tipoproduto($this->tipoproduto_id);
    
        // returns the associated object
        return $this->tipoproduto;
    }
    
    
    /**
     * Method set_tipoaquisicao
     * Sample of usage: $bens->tipoaquisicao = $object;
     * @param $object Instance of Tipoaquisicao
     */
    public function set_tipoaquisicao(Tipoaquisicao $object)
    {
        $this->tipoaquisicao = $object;
        $this->tipoaquisicao_id = $object->id;
    }
    
    /**
     * Method get_tipoaquisicao
     * Sample of usage: $bens->tipoaquisicao->attribute;
     * @returns Tipoaquisicao instance
     */
    public function get_tipoaquisicao()
    {
        // loads the associated object
        if (empty($this->tipoaquisicao))
            $this->tipoaquisicao = new Tipoaquisicao($this->tipoaquisicao_id);
    
        // returns the associated object
        return $this->tipoaquisicao;
    }

}
