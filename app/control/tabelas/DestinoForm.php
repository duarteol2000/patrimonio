<?php
class DestinoForm extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Destino para Movimentação');
        $this->form->setClientValidation( true );
        
        $id       = new TEntry('id');
        $nome   = new TEntry('nome');
        $id->setEditable(FALSE);
        
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Descrição', 'red')], [$nome] );
        
        $nome->addValidation('Descrição', new TRequiredValidator);
        
        $this->form->addAction('Salvar', new TAction( [$this, 'onSave'] ), 'fa:save green');
        $this->form->addActionLink('Limpar', new TAction( [$this, 'onClear'] ), 'fa:eraser red');
        $this->form->addActionLink('Voltar', new TAction(array('DestinoTraitList','onReload')),'fa:table blue');
        
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
            
            $destino = new Destino;
            $destino->fromArray( (array) $data);
            $destino->store();
            
            $this->form->setData( $destino );
            
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
                $destino = new Destino($key);
                $this->form->setData($destino);
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