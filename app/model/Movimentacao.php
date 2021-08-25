<?php
/**
 * Movimentacao Active Record
 * @author  <your-name-here>
 */
class Movimentacao extends TRecord
{
    const TABLENAME = 'movimentacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $destino;
    private $motivo;
    private $bens;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('bens_id');
        parent::addAttribute('numtermomov');
        parent::addAttribute('datamov');
        parent::addAttribute('destino_id');
        parent::addAttribute('motivo_id');
        parent::addAttribute('ativo');
    }

    
    /**
     * Method set_destino
     * Sample of usage: $movimentacao->destino = $object;
     * @param $object Instance of Destino
     */
    public function set_destino(Destino $object)
    {
        $this->destino = $object;
        $this->destino_id = $object->id;
    }
    
    /**
     * Method get_destino
     * Sample of usage: $movimentacao->destino->attribute;
     * @returns Destino instance
     */
    public function get_destino()
    {
        // loads the associated object
        if (empty($this->destino))
            $this->destino = new Destino($this->destino_id);
    
        // returns the associated object
        return $this->destino;
    }
    
    
    /**
     * Method set_motivo
     * Sample of usage: $movimentacao->motivo = $object;
     * @param $object Instance of Motivo
     */
    public function set_motivo(Motivo $object)
    {
        $this->motivo = $object;
        $this->motivo_id = $object->id;
    }
    
    /**
     * Method get_motivo
     * Sample of usage: $movimentacao->motivo->attribute;
     * @returns Motivo instance
     */
    public function get_motivo()
    {
        // loads the associated object
        if (empty($this->motivo))
            $this->motivo = new Motivo($this->motivo_id);
    
        // returns the associated object
        return $this->motivo;
    }
    
    
    /**
     * Method set_bens
     * Sample of usage: $movimentacao->bens = $object;
     * @param $object Instance of Bens
     */
    public function set_bens(Bens $object)
    {
        $this->bens = $object;
        $this->bens_id = $object->id;
    }
    
    /**
     * Method get_bens
     * Sample of usage: $movimentacao->bens->attribute;
     * @returns Bens instance
     */
    public function get_bens()
    {
        // loads the associated object
        if (empty($this->bens))
            $this->bens = new Bens($this->bens_id);
    
        // returns the associated object
        return $this->bens;
    }
    


}
