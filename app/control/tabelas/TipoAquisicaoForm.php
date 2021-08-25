<?php
class TipoAquisicaoForm extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Tipo de Aquisição');
        $this->form->setClientValidation( true );
        
        $id              = new TEntry('id');
        $descaquisicao   = new TEntry('descaquisicao');
        $id->setEditable(FALSE);
        
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Descrição', 'red')], [$descaquisicao] );
        
        $descaquisicao->addValidation('Descrição', new TRequiredValidator);
        
        $this->form->addAction('Salvar', new TAction( [$this, 'onSave'] ), 'fa:save green');
        $this->form->addActionLink('Limpar', new TAction( [$this, 'onClear'] ), 'fa:eraser red');
        $this->form->addActionLink('Voltar', new TAction(array('TipoAquisicaoTraitList','onReload')),'fa:table blue');
        
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
            
            $tipoaquisicao = new Tipoaquisicao;
            $tipoaquisicao->fromArray( (array) $data);
            $tipoaquisicao->store();
            
            $this->form->setData( $tipoaquisicao );
            
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
                $tipoaquisicao = new Tipoaquisicao($key);
                $this->form->setData($tipoaquisicao);
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