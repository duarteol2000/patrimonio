<?php
class MotivoForm extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Estado do Produto');
        $this->form->setClientValidation( true );
        
        $id           = new TEntry('id');
        $motivo   = new TEntry('motivo');
        $id->setEditable(FALSE);
        
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Descrição', 'red')], [$motivo] );
        
        $motivo->addValidation('Descrição', new TRequiredValidator);
        
        $this->form->addAction('Salvar', new TAction( [$this, 'onSave'] ), 'fa:save green');
        $this->form->addActionLink('Limpar', new TAction( [$this, 'onClear'] ), 'fa:eraser red');
        $this->form->addActionLink('Voltar', new TAction(array('MotivoTraitList','onReload')),'fa:table blue');
        
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
            
            $motivo = new Motivo;
            $motivo->fromArray( (array) $data);
            $motivo->store();
            
            $this->form->setData( $motivo );
            
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
                $key        = $param['key'];
                $motivo = new Motivo($key);
                $this->form->setData($motivo);
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