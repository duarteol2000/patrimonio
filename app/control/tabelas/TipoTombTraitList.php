<?php
class TipoTombTraitList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    
    use Adianti\Base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('patrimonio');
        $this->setActiveRecord('Tipotomb');
        $this->addFilterField('descricao', 'like', 'descricao');
        $this->setDefaultOrder('id', 'asc');
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Lista Tipo de tombamento');
        
        $descricao = new TEntry('descricao');
        
        $this->form->addFields( [new TLabel('Descrição')], [$descricao] );
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Limpar', new TAction([$this, 'clear']), 'fa:eraser red');
        $this->form->addActionLink('Novo', new TAction( ['TipoTombForm', 'onClear']), 'fa:plus-circle green');
        
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        
        $col_id        = new TDataGridColumn('id', 'Cód', 'right', '10%');
        $col_descricao = new TDataGridColumn('descricao', 'Descrição', 'left', '60%');
        
        $col_id->setAction( new TAction( [$this, 'onReload'] ), ['order' => 'id']);
        $col_descricao->setAction( new TAction( [$this, 'onReload'] ), ['order' => 'descricao']);
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_descricao);
        
        $action1 = new TDataGridAction( ['TipoTombForm', 'onEdit'], [ 'key' => '{id}'] );
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