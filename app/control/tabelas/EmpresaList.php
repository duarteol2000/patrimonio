<?php
class EmpresaList extends TPage
{
    private $datagrid;
    private $pageNavigation;
    
    use Adianti\Base\AdiantiStandardListTrait;    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('patrimonio');
        $this->setActiveRecord('Empresa');
        $this->setDefaultOrder('id', 'asc');
        $this->addFilterField('id', '=', 'id');
        $this->addFilterField('cpf_cnpj', '=', 'cpf_cnpj');
        $this->addFilterField('nome', 'like', 'nome');
        $this->addFilterField('logradouro', 'like', 'logradouro');
        
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width:100%';
        
        $col_id       = new TDataGridColumn('id', 'Cód', 'center', '10%');
        $col_cpf_cnpj = new TDataGridColumn('cpf_cnpj', 'Cpf/Cnpj', 'left', '28%');
        $col_nome     = new TDataGridColumn('nome', 'Nome', 'left', '28%');
        $col_endereco = new TDataGridColumn('logradouro', 'Logradouro', 'left', '28%');

        $col_id->setAction( new TAction([$this, 'onReload']), ['order' => 'id'] );
        $col_cpf_cnpj->setAction( new TAction([$this, 'onReload']), ['order' => 'cpf_cnpj'] );
        $col_nome->setAction( new TAction([$this, 'onReload']), ['order' => 'nome'] );
        $col_endereco->setAction( new TAction([$this, 'onReload']), ['order' => 'logradouro'] );
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_cpf_cnpj);
        $this->datagrid->addColumn($col_endereco);
        
        
        $action1 = new TDataGridAction( ['EmpresaForm', 'onEdit'], ['key' => '{id}', 'register_state' => 'false'] );
        $action2 = new TDataGridAction( [$this, 'onDelete'], ['key' => '{id}']);
        $this->datagrid->addAction($action1, 'Editar', 'fa:edit blue');
        $this->datagrid->addAction($action2, 'Excluir', 'fa:trash-alt red');
        
        $this->datagrid->createModel();
        
        
        $this->form = new TForm;
        $this->form->add($this->datagrid);
        
        
        $id         =  new TEntry('id');
        $cpf_cnpj   =  new TEntry('cpf_cnpj');
        $nome       =  new TEntry('nome');
        $logradouro =  new TEntry('endereco');
        
        $id->exitOnEnter();
        $nome->exitOnEnter();
        $cpf_cnpj->exitOnEnter();
        $logradouro->exitOnEnter();
        
        $id->setSize('100%');
        $cpf_cnpj->setSize('100%');
        $nome->setSize('100%');
        $logradouro->setSize('100%');
        
        $id->tabindex = -1;
        $cpf_cnpj->tabindex = -1;
        $nome->tabindex = -1;
        $logradouro->tabindex = -1;

        $id->setExitAction( new TAction( [ $this, 'onSearch' ], ['static' => '1']) );
        $cpf_cnpj->setExitAction( new TAction( [ $this, 'onSearch' ], ['static' => '1']) );
        $nome->setExitAction( new TAction( [ $this, 'onSearch' ], ['static' => '1']) );
        $logradouro->setExitAction( new TAction( [ $this, 'onSearch' ], ['static' => '1']) );
        
        $tr = new TElement('tr');
        $this->datagrid->prependRow($tr);
        
        $tr->add( TElement::tag('td', '') );
        $tr->add( TElement::tag('td', '') );
        $tr->add( TElement::tag('td', $id) );
        $tr->add( TElement::tag('td', $nome) );
        $tr->add( TElement::tag('td', $cpf_cnpj) );
        $tr->add( TElement::tag('td', $logradouro) );
        
        $this->form->addField($id);
        $this->form->addField($cpf_cnpj);
        $this->form->addField($nome);
        $this->form->addField($logradouro);
        
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ));
        $this->pageNavigation->enableCounters();
        
        
        $panel = new TPanelGroup('Empresas');
        $panel->add($this->form);
        $panel->addFooter($this->pageNavigation);
        
        $dropdown = new TDropDown('Exportar', 'fa:list');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( 'Salvar como CSV', new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table fa-fw blue' );
        $dropdown->addAction( 'Salvar como PDF', new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf fa-fw red' );
        $dropdown->addAction( 'Salvar como XML', new TAction([$this, 'onExportXML'], ['register_state' => 'false', 'static'=>'1']), 'fa:code fa-fw green' );
        
        
        $panel->addHeaderWidget($dropdown);
        $panel->addHeaderActionLink('Novo', new TAction(['EmpresaForm', 'onClear'], ['register_state'=>'false']), 'fa:plus green');
        
        
        parent::add( $panel );
    }
}