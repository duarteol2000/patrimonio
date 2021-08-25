<?php
class TipoTombForm extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Tipo de Tombamento');
        $this->form->setClientValidation( true );
        
        $id          = new TEntry('id');
        $descricao   = new TEntry('descricao');
        $id->setEditable(FALSE);
        
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Descrição', 'red')], [$descricao] );
        
        $descricao->addValidation('Descrição', new TRequiredValidator);
        
        $this->form->addAction('Salvar', new TAction( [$this, 'onSave'] ), 'fa:save green');
        $this->form->addActionLink('Limpar', new TAction( [$this, 'onClear'] ), 'fa:eraser red');
        $this->form->addActionLink('Voltar', new TAction(array('TipoTombTraitList','onReload')),'fa:table blue');
        
        parent::add($this->form);
    }
    
  
    public function onClear()
    {
        $this->form->clear(true);
    }
    
    public function onSave($param)
    {
        try
        {
            TTransaction::open('patrimonio');
            
            $this->form->validate();
            
            $data = $this->form->getData();
            
            $tipotomb = new Tipotomb;
            $tipotomb->fromArray( (array) $data);
            $tipotomb->store();
            
            $this->form->setData( $tipotomb );
            
            new TMessage('info', 'Registro salvo com sucesso');
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('patrimonio');
            
            if (isset($param['key']))
            {
                $key      = $param['key'];
                $tipotomb = new Tipotomb($key);
                $this->form->setData($tipotomb);
            }
            else
            {
                $this->form->clear(true);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}