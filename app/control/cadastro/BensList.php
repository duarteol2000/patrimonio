<?php
class BensList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    
    use Adianti\Base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('patrimonio');
        $this->setActiveRecord('Bens');
        $this->addFilterField('id', '=', 'id');
        $this->addFilterField('num_tombamento', '=', 'num_tombamento');
        $this->addFilterField('data_receb', '>=', 'data_de');
        $this->addFilterField('data_receb', '<=', 'data_ate');
        $this->addFilterField('descricao', 'like', 'descricao');
        $this->setDefaultOrder('id', 'asc');
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Lista de Bens');
        
        $id              = new TEntry('id');
        $num_tombamento  = new TEntry('num_tombamento');
        $descricao       = new TEntry('descricao');
        $data_de         = new TDate('data_de');
        $data_ate        = new TDate('data_ate');
        
        //$empresa_id = new TDBUniqueSearch('empresa_id', 'patrimonio', 'Empresa', 'id', 'nome');
        //$empresa_id->setMinLength(1);
        
        $this->form->addFields( [new TLabel('Código')], [$id] );
        $this->form->addFields( [new TLabel('Tombamento')], [$num_tombamento] );
        $this->form->addFields( [new TLabel('Data (de)')], [$data_de], [new TLabel('Data (até)')], [$data_ate] );
        //$this->form->addFields( [new TLabel('Empresa')], [$empresa_id] );
        $this->form->addFields( [new TLabel('Descrição')], [$descricao] );
        
        
        $id->setSize('50%');
        $num_tombamento->setSize('50%');
        $descricao->setSize('100%');
        $data_de->setSize('100%');
        $data_ate->setSize('100%');
        $data_de->setMask('dd/mm/yyyy');
        $data_ate->setMask('dd/mm/yyyy');
        $data_de->setDatabaseMask('yyyy-mm-dd');
        $data_ate->setDatabaseMask('yyyy-mm-dd');
        
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        $this->form->addAction( 'Buscar', new TAction([$this, 'onSearch']), 'fa:search');
        
        $this->form->addAction( 'Imprimir', new TAction([$this, 'onPrint']), 'fa:print');
        
        $this->form->addActionLink( 'Novo', new TAction(['BensForm', 'onEdit']), 'fa:plus green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        
        $col_id             = new TDataGridColumn('id', 'Código', 'center', '10%');
        $col_num_tombamento = new TDataGridColumn('num_tombamento', 'Tombamento', 'center', '10%');
        $col_data           = new TDataGridColumn('data_receb', 'Data', 'center', '20%');
        $col_descricao      = new TDataGridColumn('descricao', 'Descrição', 'left', '50%');
        //$col_empresa        = new TDataGridColumn('empresa->nome', 'Empresa', 'left', '50%');

        //$col_total          = new TDataGridColumn('total', 'Total', 'right', '20%');
        
        //$col_total->setTransformer( function($total, $object, $row) {
        //    if (is_numeric($total))
        //    {
        //        return 'R$ ' . number_format($total, 2, ',', '.');
        //    } 
        //    return $total;
        //});
        
        $col_data->setTransformer( function($data, $object, $row) {
            $date = new DateTime($data);
            return $date->format('d/m/Y');
        });
        
        $col_id->setAction( new TAction([$this, 'onReload']), ['order' => 'id']);
        $col_data->setAction( new TAction([$this, 'onReload']), ['order' => 'data_receb']);
        
        $this->datagrid->addColumn( $col_id );
        $this->datagrid->addColumn( $col_num_tombamento );
        $this->datagrid->addColumn( $col_data );
        $this->datagrid->addColumn( $col_descricao );
        //$this->datagrid->addColumn( $col_empresa );
        //$this->datagrid->addColumn( $col_total );
        
        $action_view   = new TDataGridAction( ['BensFormView', 'onView'], ['key' => '{id}', 'register_state' => 'false']);
        $action_edit   = new TDataGridAction( ['BensForm', 'onEdit'], ['key' => '{id}']);
        //$action_delete = new TDataGridAction( [$this, 'onDelete'], ['key' => '{id}']);
        
        $this->datagrid->addAction( $action_view, 'Visualizar', 'fa:search green');
        $this->datagrid->addAction( $action_edit, 'Editar', 'fa:edit blue');
        //$this->datagrid->addAction( $action_delete, 'Excluir', 'fa:trash-alt red');
        
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction( new TAction( [$this, 'onReload']) );
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        $vbox->add($panel);
        
        parent::add( $vbox );
    }
    public function onPrint(){
        TApplication::loadPage('BensPorSetor');
    }
}