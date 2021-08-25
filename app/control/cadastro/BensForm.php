<?php
/**
 * BensForm Master/Detail
 * @author  <your name here>
 */
class BensForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Bens');
        $this->form->setFormTitle('<h4><b>Bens Patrimoniais</b></h4>');
        //$this->form->style = "ont-weight:bold; font-style: ilatic;color: red;";
        $this->form->setFieldSizes('100%');
        $this->form->generateAria(); // automatic aria-label
        
        
        // master fields
        $id               = new TEntry('id');
        $num_tombamento   = new TEntry('num_tombamento');
        $descricao        = new TEntry('descricao');
        $empresa_id       = new TDBUniqueSearch('empresa_id', 'patrimonio', 'Empresa', 'id', 'nome');
        $data_receb       = new TDate('data_receb');
        $tipoaquisicao_id = new TDBCombo('tipoaquisicao_id', 'patrimonio', 'Tipoaquisicao', 'id', 'descaquisicao');
        $estadoprod_id    = new TDBCombo('estadoprod_id', 'patrimonio', 'Estadoprod', 'id', 'estadoprod');
        $numdoc           = new TEntry('numdoc');
        $num_notafiscal   = new TEntry('num_notafiscal');
        $valordoben       = new TEntry('valordoben');
        //$ativo            = new TEntry('ativo');
        $tipotomb_id      = new TDBCombo('tipotomb_id', 'patrimonio', 'Tipotomb', 'id', 'descricao');
        $tipoproduto_id   = new TDBCombo('tipoproduto_id', 'patrimonio', 'Tipoproduto', 'id', 'descricao');

        $empresa_id->setMinLength(1);

        // detail fields
        $detail_uniqid      = new THidden('detail_uniqid');
        $detail_id          = new THidden('detail_id');
        $detail_numtermomov = new TEntry('detail_numtermomov');
        $detail_datamov     = new TDate('detail_datamov');
        $detail_destino_id  = new TDBCombo('detail_destino_id', 'patrimonio', 'Destino', 'id', 'nome');
        
        $detail_motivo_id   = new TDBCombo('detail_motivo_id', 'patrimonio', 'Motivo', 'id', 'motivo');
        $detail_ativo       = new TCombo('detail_ativo');
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        $detail_ativo->setSize('100%');
        $detail_ativo->addItems( ['S' => 'S', 'N' => 'N'] );
        
        //definindo valor inicial
        $detail_ativo->setValue('S');
        
        // definição de formato numerico
        $valordoben->setNumericMask(2,',', '.', true);
        
        //
        //$detail_destino_id->setMinLength(1);
        //$detail_motivo_id->setMinLength(1);
        
        $row = $this->form->addFields( [ new TLabel('<b>Codigo</b>')        ,   $id ],
                                       [ new TLabel('<b>Num.Tombamento</b>'),   $num_tombamento ],
                                       [ new TLabel('<b>Descrição</b>')     ,   $descricao ] );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-8' ];
        
        $row = $this->form->addFields( [ new TLabel('<b>Empresa</b>'),  $empresa_id ] );
        $row->layout = ['col-sm-12'];
        
        $row = $this->form->addFields( [ new TLabel('<b>Data do Receb.</b>')    , $data_receb ],
                                       [ new TLabel('<b>Tipo Aquisição</b>')    , $tipoaquisicao_id ],
                                       [ new TLabel('<b>Estado do Produto</b>') , $estadoprod_id ],
                                       [ new TLabel('<b>Numero Doc.</b>')       , $numdoc ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3','col-sm-2'];
                                       
        $row = $this->form->addFields( [ new TLabel('<b>N. Fiscal</b>')       ,  $num_notafiscal ],
                                       [ new TLabel('<b>Valor do Produto</b>'),  $valordoben ],
                                       [ new TLabel('<b>Tp. Patrim.</b>')     ,  $tipotomb_id ],
                                       [ new TLabel('<b>Tp. do Produto</b>')  ,  $tipoproduto_id ]);
        $row->layout = [ 'col-sm-2', 'col-sm-2','col-sm-3', 'col-sm-2' ];
        
        // detail fields
        //$this->form->addContent( ['<br> </br>'] );
        $this->form->addContent( ['<h4><b>Movimentação</b></h4>'] );
        $this->form->addFields( [$detail_uniqid] );
        $this->form->addFields( [$detail_id] );
        
        $add = TButton::create('add', [$this, 'onDetailAdd'], 'Incluir', 'fa:plus-circle green');

        $add->getAction()->setParameter('static','1');

        $row = $this->form->addFields( [ new TLabel('<b>Termo</b>')     , $detail_numtermomov ],
                                       [ new TLabel('<b>Data Mov.</b>'), $detail_datamov ],
                                       [ new TLabel('<b>Destino</b>')     , $detail_destino_id ],
                                       [ new TLabel('<b>Motivo</b>')      , $detail_motivo_id ],
                                       [ new TLabel('<b>Ativo</b>')       , $detail_ativo ],
                                       [ new TLabel('')                   , $add]);
                                       
        $row->layout = [ 'col-sm-2', 'col-sm-2','col-sm-3', 'col-sm-3','col-sm-1','col-sm-1','col-sm-1'];                               
        
        /*
        $add = TButton::create('add', [$this, 'onDetailAdd'], 'Incluir Mov.', 'fa:plus-circle green');
        $add->getAction()->setParameter('static','1');
        $this->form->addFields( [], [$add] );
        */
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->setId('Movimentacao_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        
        $col_uniq        = new TDataGridColumn( 'uniqid', 'Uniqid', 'center', '10%');
        $col_id          = new TDataGridColumn( 'id', 'Cód', 'center', '10%');
        $col_numter      = new TDataGridColumn('numtermomov', 'N.Termo', 'left', 10) ;
        $col_datmov      = new TDataGridColumn('datamov', 'Data.Mov', 'left', 50) ;
        $col_destid      = new TDataGridColumn('destino_id', 'ID', 'left', 10) ;
        $col_destdesc    = new TDataGridColumn('destino_id', 'Destino', 'left', 100) ;
        $col_motid       = new TDataGridColumn('motivo_id', 'M.ID', 'left', 10) ;
        $col_motdesc     = new TDataGridColumn('motivo_id', 'Motivo', 'left', 10) ;
        $col_atv         = new TDataGridColumn('ativo', 'Ativo', 'left', 10) ;
        
        $this->detail_list->addColumn( $col_uniq );
        $this->detail_list->addColumn( $col_id );
        $this->detail_list->addColumn( $col_numter );
        $this->detail_list->addColumn( $col_datmov );
        $this->detail_list->addColumn( $col_destid );
        $this->detail_list->addColumn( $col_destdesc );
        $this->detail_list->addColumn( $col_motid );
        $this->detail_list->addColumn( $col_motdesc );
        $this->detail_list->addColumn( $col_atv );
        
        $col_destdesc->setTransformer(function($value) {
            return Destino::findInTransaction('patrimonio', $value)->nome;
        });
        
        $col_motdesc->setTransformer(function($value) {
            return Motivo::findInTransaction('patrimonio', $value)->motivo;
        });
        
        $col_id->setVisibility(false);
        $col_uniq->setVisibility(false);
        $col_destid->setVisibility(false);
        $col_motid->setVisibility(false);

        // detail actions
        $action1 = new TDataGridAction([$this, 'onDetailEdit'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDetailDelete']);
        $action2->setField('uniqid');
        
        // add the actions to the datagrid
        $this->detail_list->addAction($action1, _t('Edit'), 'fa:edit blue');
        $this->detail_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
        $this->form->addAction( 'Save',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        $this->form->addAction( 'Clear', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addActionLink('Voltar', new TAction(array('BensList','onReload')),'fa:table blue');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    
    /**
     * Clear form
     * @param $param URL parameters
     */
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Add detail item
     * @param $param URL parameters
     */
    public function onDetailAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            /** validation sample
            if (empty($data->fieldX))
            {
                throw new Exception('The field fieldX is required');
            }
            **/
            
            $uniqid = !empty($data->detail_uniqid) ? $data->detail_uniqid : uniqid();
            
            //echo '<pre>';
            //print_r($data);
            //echo '</pre>';
            
            $grid_data = [];
            $grid_data['uniqid']      = $uniqid;
            $grid_data['id']          = $data->detail_id;
            $grid_data['numtermomov'] = $data->detail_numtermomov;
            $grid_data['datamov']     = $data->detail_datamov;
            $grid_data['destino_id']  = $data->detail_destino_id;   //.' - '.$data->destino->nome;
            $grid_data['motivo_id']   = $data->detail_motivo_id;
            $grid_data['ativo']       = $data->detail_ativo;
            
            // insert row dynamically
            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('Movimentacao_list', $uniqid, $row);
            
            // clear detail form fields
            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_numtermomov = '';
            $data->detail_datamov = '';
            $data->detail_destino_id = '';
            $data->detail_motivo_id = '';
            $data->detail_ativo = '';
            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_Bens', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Edit detail item
     * @param $param URL parameters
     */
    public static function onDetailEdit( $param )
    {
        //echo'<pre>';
        //print_r($param);
        //echo'<pre>';
        
        $data = new stdClass;
        $data->detail_uniqid      = $param['uniqid'];
        $data->detail_id          = $param['id'];
        $data->detail_numtermomov = $param['numtermomov'];
        $data->detail_datamov     = $param['datamov'];
        $data->detail_destino_id  = $param['destino_id'];
        $data->detail_motivo_id   = $param['motivo_id'];
        $data->detail_ativo       = $param['ativo'];
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Bens', $data, false, false );
    }
    
    /**
     * Delete detail item
     * @param $param URL parameters
     */
    public static function onDetailDelete( $param )
    {
        // clear detail form fields
        $data = new stdClass;
        $data->detail_uniqid = '';
        $data->detail_id = '';
        $data->detail_numtermomov = '';
        $data->detail_datamov = '';
        $data->detail_destino_id = '';
        $data->detail_motivo_id = '';
        $data->detail_ativo = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Bens', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('Movimentacao_list', $param['uniqid']);
    }
    
    /**
     * Load Master/Detail data from database to form
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('patrimonio');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Bens($key);
                $items  = Movimentacao::where('bens_id', '=', $key)->load();
                
                foreach( $items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form to database
     */
    public function onSave($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('patrimonio');
            
            $data = $this->form->getData();
            $this->form->validate();
            
            $master = new Bens;
            $master->fromArray( (array) $data);
            $master->store();
            
            Movimentacao::where('bens_id', '=', $master->id)->delete();
            
            if( $param['Movimentacao_list_numtermomov'] ?? false )
            {
                foreach( $param['Movimentacao_list_numtermomov'] as $key => $item_id )
                {
                    $detail = new Movimentacao;
                    $detail->numtermomov = $param['Movimentacao_list_numtermomov'][$key];
                    $detail->datamov     = $param['Movimentacao_list_datamov'][$key];
                    $detail->destino_id  = $param['Movimentacao_list_destino_id'][$key];
                    $detail->motivo_id   = $param['Movimentacao_list_motivo_id'][$key];
                    $detail->ativo       = $param['Movimentacao_list_ativo'][$key];
                    $detail->bens_id = $master->id;
                    $detail->store();
                }
            }
             
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_Bens', (object) ['id' => $master->id]);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
}
