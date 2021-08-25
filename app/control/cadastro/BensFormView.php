<?php
class BensFormView extends TPage
{
    private $form;
    
    public function __construct($param)
    {
        parent::__construct();
        
        parent::setTargetContainer('adianti_right_panel');
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Lista Movimento de Bens');
        
        $this->form->setColumnClasses( 2, ['col-sm-3', 'col-sm-9'] );
        
        $this->form->addHeaderActionLink('Imprimir', new TAction([$this, 'onPrint'], ['key'=>$param['key'], 'static' => '1', 'register_state' => 'false']), 'far:file-pdf red'); 
        $this->form->addHeaderActionLink('Editar', new TAction([$this, 'onEdit'], ['key'=>$param['key'], 'register_state' => 'false']), 'far:edit blue'); 
        $this->form->addHeaderActionLink('Fechar', new TAction([$this, 'onClose']), 'fa:times red');
        parent::add($this->form);
    }
    
    public function onView($param)
    {
        try
        {
            /*
            Área de Teste
            */
            $this->form->setFormTitle('Lista Movimento de Bens');
            $this->form->setFieldSizes('100%');
            $this->form->generateAria(); // automatic aria-label
            //-----------------------------------------------------------------------
        
            TTransaction::open('patrimonio');
            
            $bens = new Bens( $param['key'] );
            
            //labels
            $lbl_cod      = new TLabel('Codigo');
            $lbl_numtomb  = new TLabel('Nu.Tomb');
            $lbl_desc     = new TLabel('Descrição');
            $lbl_datarec  = new TLabel('Data de Rec');
            $lbl_numdoc   = new TLabel('Num. Doc');
            $lbl_notafisc = new TLabel('N. Fiscal');
            $lbl_valprod  = new TLabel('Valor do Prod');
            
            //Styles
            $lbl_cod->style = "font-weight: bold;";
            $lbl_numtomb->style = "font-weight: bold;";
            $lbl_desc->style = "font-weight: bold;";
            $lbl_datarec->style = "font-weight: bold;";
            $lbl_numdoc->style = "font-weight: bold;";
            $lbl_notafisc->style = "font-weight: bold;";
            $lbl_valprod->style = "font-weight: bold;";
            
            //alinhar o valor a direita
            $lbl_valordoben = $bens->valordoben;
            //$lbl_valordoben->style = 'text-align: right;';
            // Linhas
            $row = $this->form->addFields( [ $lbl_cod     , new TTextDisplay($bens->id, '#0600FF') ],
                                           [ $lbl_numtomb , new TTextDisplay($bens->num_tombamento, '#0600FF') ],
                                           [ $lbl_desc    , new TTextDisplay($bens->descricao, '#0600FF') ] );
            $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-6' ];
            //
            $row = $this->form->addFields( [ $lbl_datarec  , new TTextDisplay( $bens->data_receb, '#0600FF')],
                                           [ $lbl_numdoc   , new TTextDisplay($bens->numdoc, '#0600FF') ],
                                           [ $lbl_notafisc , new TTextDisplay($bens->num_notafiscal, '#0600FF') ],
                                           [ $lbl_valprod  , new TTextDisplay(number_format($lbl_valordoben,'2',',','.'), '#0600FF','','') ] );
                                           
            $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-2', 'col-sm-4' ];
                        
            /*
            $this->form->addFields( [ new TLabel('Código:')], [ new TTextDisplay( $bens->id, '#333') ]); 
            $this->form->addFields( [ new TLabel('Tombamento:')], [ new TTextDisplay( $bens->num_tombamento, '#333') ] );
            $this->form->addFields( [ new TLabel('Data de Rec:')], [ new TTextDisplay( $bens->data_receb, '#333') ]);
            $this->form->addFields( [ new TLabel('Num. Doc:')], [ new TTextDisplay( $bens->numdoc, '#333') ] );
            $this->form->addFields( [ new TLabel('Nota Fiscal:')], [ new TTextDisplay( $bens->num_notafiscal, '#333') ] );
            $this->form->addFields( [ new TLabel('Valor do Prod:')], [ new TTextDisplay( $bens->valordoben, '#333') ] );
            $this->form->addFields( [ new TLabel('Empresa')], [ new TTextDisplay( $bens->empresa->nome, '#333') ] );
            */
            
            
            $list = new BootstrapDatagridWrapper( new TDataGrid);
            $list->style = 'width:100%';
            
            $col_numtermomov  = new TDataGridColumn('numtermomov', 'Nº Termo', 'left');
            $col_datamov      = new TDataGridColumn('datamov', 'Data Movim', 'right');
            $col_destino_id   = new TDataGridColumn('destino->nome', 'Destino', 'center');
            $col_motivo_id    = new TDataGridColumn('motivo->motivo', 'Motivo', 'right');
            //$col_total    = new TDataGridColumn('total', 'Total', 'right');
            
            $list->addColumn( $col_numtermomov );
            $list->addColumn( $col_datamov );
            $list->addColumn( $col_destino_id );
            $list->addColumn( $col_motivo_id );
            //$list->addColumn( $col_total );
            
            //$format = function($valor) {
            //    if (is_numeric($valor)) {
            //        return 'R$ '. number_format($valor, 2, ',', '.');
            //    }
            //    return $valor;
            //};
            
            //$col_datamov->setTransformer( $format );
            //$col_motivo_id->setTransformer( $format );
            //$col_total->setTransformer( $format );
            
            //$col_total->setTotalFunction( function($valores) {
            //    return array_sum( (array) $valores);
            //});
            $list->createModel();
            
            
            $itens = Movimentacao::where('bens_id', '=', $bens->id)->load();
            
            $list->addItems($itens);
            
            $panel = new TPanelGroup('Lista de Movimentações');
            $panel->add($list);
            
            $this->form->addContent( [$panel] );
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    
    public function onPrint($param)
    {
        try
        {
            $this->onView($param);
            
            // string with HTML contents
            $html = clone $this->form;
            $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $file = 'app/output/bens.pdf';
            
            // write and open file
            file_put_contents($file, $dompdf->output());
            
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file.'?rndval='.uniqid();
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function onEdit($param)
    {
        unset($param['static']);
        AdiantiCoreApplication::loadPage('BensForm', 'onEdit', $param);
    }
    
    public static function onClose($param)
    {
        TScript::create('Template.closeRightPanel()');
    }
}