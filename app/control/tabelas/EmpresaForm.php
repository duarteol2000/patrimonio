<?php
class EmpresaForm extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        parent::setTargetContainer('adianti_right_panel');
        
        $this->form = new BootstrapFormBuilder('form_empresa');
        $this->form->setFormTitle('Empresa');
        
        $id           = new TEntry('id');
        $cpf_cnpj     = new TEntry('cpf_cnpj');
        $nome         = new TEntry('nome');
        $nomefantasia = new TEntry('nomefantasia');
        $contato1     = new TEntry('contato1');
        $contato2     = new TEntry('contato2');
        $cep          = new TEntry('cep');
        $logradouro   = new TEntry('logradouro');
        $numero       = new TEntry('numero');
        $complemento  = new TEntry('complemento');
        $bairro       = new TEntry('bairro');
        $estado       = new TEntry('estado');
        $cidade       = new TEntry('cidade');
        
        $id->setEditable(false);
        $id->setSize('30%');
        $nome->setSize('100%');
        $cpf_cnpj->setSize('70%');
        $nomefantasia->setSize('100%');
        $contato1->setSize('100%');
        $contato2->setSize('100%');
        $cep->setSize('100%');
        $logradouro->setSize('100%');
        $numero->setSize('60%');
        $complemento->setSize('100%');
        $bairro->setSize('100%');
        $cidade->setSize('100%');
        $estado->setSize('100%');
        
        
        
        $this->form->appendPage('Dados da Empresa');
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('CPF/CNPJ')], [$cpf_cnpj]);
        $this->form->addFields( [new TLabel('Nome')], [$nome] );
        $this->form->addFields( [new TLabel('Nome Fantasia')], [$nomefantasia] );
        $this->form->addFields( [new TLabel('Contato 1')], [$contato1] );
        $this->form->addFields( [new TLabel('Contato 2')], [$contato2] );
        $this->form->addFields( [new TLabel('Cep')], [$cep],
                                [new TLabel('Logradouro')], [$logradouro]);  
                                
        $this->form->addFields( [new TLabel('Complemento')], [$complemento],
                                [new TLabel('Numero')], [$numero]);
                                
        $this->form->addFields( [new TLabel('Bairro')], [$bairro]); 
        $this->form->addFields( [new TLabel('Cidade')], [$cidade],
                                [new TLabel('Estado')], [$estado]);
        
        $this->form->addAction( 'Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addHeaderActionLink( 'Fechar', new TAction( [$this, 'onClose']), 'fa:times red');
        
        parent::add($this->form);
    
    }
    
    public static function onSave($param)
    {
        try
        {
            TTransaction::open('patrimonio');
            $empresa = new Empresa;
            $empresa->fromArray( $param );
            $empresa->store();
            
            $data = new stdClass;
            $data->id = $empresa->id;
            TForm::sendData('form_empresa', $data);
            
            TScript::create('Template.closeRightPanel()');
            
            $pos_action = new TAction( ['EmpresaList', 'onReload'] );
            
            new TMessage('info', 'Registro gravado com sucesso', $pos_action);
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public function onClear($param)
    {
        $this->form->clear();
        
    }
    
    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                TTransaction::open('patrimonio');
                
                $empresa = new Empresa($param['key']);
                
                $this->form->setData( $empresa );
                
                TTransaction::close();
            }
            else
            {
                $this->onClear($param);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function onClose($param)
    {
        TScript::create('Template.closeRightPanel()');
    }
}
