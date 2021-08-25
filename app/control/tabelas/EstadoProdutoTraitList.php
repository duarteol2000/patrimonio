<?php
class EstadoProdutoTraitList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    
    use Adianti\Base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('patrimonio');
        $this->setActiveRecord('Estadoprod');
        $this->addFilterField('estadoprod', 'like', 'estadoprod');
        $this->setDefaultOrder('id', 'asc');
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Lista Estado do Produto');
        
        $estadoprod = new TEntry('estadoprod');
        
        $this->form->addFields( [new TLabel('Descrição')], [$estadoprod] );
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Limpar', new TAction([$this, 'clear']), 'fa:eraser red');
        $this->form->addActionLink('Novo', new TAction( ['EstadoProdutoForm', 'onClear']), 'fa:plus-circle green');
        
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        
        $col_id            = new TDataGridColumn('id', 'Cód', 'right', '10%');
        $col_estadoprod = new TDataGridColumn('estadoprod', 'Descrição', 'left', '60%');
        
        $col_id->setAction( new TAction( [$this, 'onReload'] ), ['order' => 'id']);
        $col_estadoprod->setAction( new TAction( [$this, 'onReload'] ), ['order' => 'estadoprod']);
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_estadoprod);
        
        $action1 = new TDataGridAction( ['EstadoProdutoForm', 'onEdit'], [ 'key' => '{id}'] );
        $action2 = new TDataGridAction( [$this, 'onDelete'], [ 'key' => '{id}'] );
        
        $this->datagrid->addAction( $action1, 'Editar', 'fa:edit blue');
        $this->datagrid->addAction( $action2, 'Excluir', 'fa:trash-alt red');
        
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction( new TAction([$this, 'onReload'] ));
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        $vbox->add($panel);
        
        parent::add( $vbox );
    }
    
    public function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}