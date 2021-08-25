<?php
class BensPorSetor extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Lista de Bens por Tipo e Setor');
        
        $combo_destino   = new TDBUniqueSearch('destino_id', 'patrimonio', 'Destino', 'id', 'nome');
        $combo_categoria = new TDBCombo('categoria_id', 'patrimonio', 'Categoria', 'id', 'descricao'  );
        $output          = new TRadioGroup('output');

        $combo_categoria->setChangeAction( new TAction( array($this, 'onChangeAction')) );

        $criteria = new TCriteria;
        $criteria->add(new TFilter('categoria_id','=',$combo_categoria->id));

        $combo_tpprod    = new TDBCombo('combo_tpprod', 'patrimonio', 'Tipoproduto', 'id', 'descricao','descricao', $criteria);

        $this->form->addFields( [new TLabel('Setor')]    , [$combo_destino] );
        $this->form->addFields( [new TLabel('Categoria')], [$combo_categoria] );
        $this->form->addFields( [new TLabel('Tipo')]     , [$combo_tpprod] );
        
        $this->form->addFields( [new TLabel('Formato')], [$output] );
        
        $output->setUseButton();
        $combo_destino->setMinLength(1);
        
        $output->addItems( ['html' => 'HTML', 'pdf' => 'PDF', 'rtf' => 'RTF', 'xls' => 'XLS'] );
        $output->setValue( 'pdf' );
        $output->setLayout('horizontal');
        
        $this->form->addAction('Gerar', new TAction([$this, 'onGenerate']), 'fa:download blue');
        $this->form->addActionLink('Voltar', new TAction(array('BensList','onReload')),'fa:table blue');

        parent::add( $this->form );
    }
    
    public function onGenerate($param)
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('patrimonio');
            
            // get the form data into
            $data = $this->form->getData();
            
            $repository = new TRepository('ViewBensPorSetor');
            $criteria   = new TCriteria;
            
            
            //echo'<pre>';
            //print_r($data);
            //echo'<pre>';
            //return;
            
            if ($data->destino_id)
            {
                $criteria->add(new TFilter('destino_id', '=', $data->destino_id));
            }
            if ($data->categoria_id)
            {
                $criteria->add(new TFilter('categoria_id', '=', $data->categoria_id));
                
            }
            if ($data->combo_tpprod)
            {            
                $criteria->add(new TFilter('tipoproduto_id', '=', $data->combo_tpprod));
                
            }

            
            $rows = $repository->load($criteria);
            $format  = $data->output;            
             
             //echo '<pre>';
             //print_r($rows);
             //echo '<pre>';                       
             //return;
             
             
            if ($rows)
            {
                //$widths = [40, 200, 80, 120, 80];
                $widths = [800, null, null,null, null];
                                
                switch ($data->output)
                {
                    case 'html':
                        $table = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $table = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        $table = new TTableWriterRTF($widths);
                        break;
                    case 'xls':
                        $table = new TTableWriterXLS($widths);
                        break;
                }
                // id, nome, categoria, email, nascimento
            
                if (!empty($table))
                {
                    $table->addStyle('header', 'Helvetica', '16', 'B', '#ffffff', '#4B5D8E');
                    $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                    $table->addStyle('datap',  'Helvetica', '10', 'B',  '#000000', '#E3E3E3', 'LR');
                    $table->addStyle('datai',  'Helvetica', '10', '',  '#000000', '#ffffff', 'LR');
                    $table->addStyle('footer', 'Helvetica', '10', '',  '#2B2B2B', '#B4CAFF');
                }
                
                $table->setHeaderCallback( function($table) {
                    $table->addRow();
                    $table->addCell('Listagem de Bens Patrimoniais por Setor', 'center', 'header', 5);
                    $table->addRow();
                    $table->addCell('Setor', 'left', 'title');
                    $table->addRow();
                    $table->addCell('Bem Patrimonial', 'left', 'title');
                });
                
                $table->setFooterCallback( function ($table) {
                    $table->addRow();
                    $table->addCell(date('d/m/Y H:i:s'), 'right', 'footer', 5);
                });
                
                $colore = true;
                $previous_dest = null;
                //inclui
                
                foreach ($rows as $row)
                {
                    
                    $previous_cat = null;
                    if ($row->destino_id != $previous_dest) {  // checa se destino se é diferente do anterior
                        $contador=0;
                        $style = 'datap';
                        $table->addRow();
                        $table->addCell( $row->destino_id.' - '.$row->nome, 'left', $style);
                        $style = 'datai';
                    }
                    $contador++;
                    $table->addRow();
                    $table->addCell( '     '.$contador.': '.$row->num_tombamento.' - '.$row->descricao.'   ('.$row->estadoprod.')', 'left', $style);
                    
                    $previous_dest = $row->destino_id;     
                    $colore = !$colore;
                }
                
                $output = 'app/output/tabular.'.$data->output;
                
                if (!file_exists($output) OR is_writable($output))
                {
                    $table->save($output);
                    parent::openFile($output);
                    
                    new TMessage('info', 'Relatório gerado com sucesso');
                }
                else
                {
                    throw new Exception('Permissão negada: ' . $output);
                }
                
                return;
                
            }
            
            $this->form->setData($data);
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    static function onChangeAction($param)
{
        try
           {
              //TTransaction::open('centinel');      //base de dados
              if ($param['categoria_id'])            //se existe parâmetro da primeira combo(nome da primeira combo)
              {
                 $criteria = TCriteria::create( ['categoria_id' => $param['categoria_id'] ] );  
                 TDBCombo::reloadFromModel('my_form','combo_tpprod','patrimonio','Tipoproduto','id','{descricao}', 'descricao',$criteria,TRUE);
            }
            else
            {
              TCombo::clearField('my_form', 'combo_tpprod'); //reload do formulario frm_empresa na segunda combo "natureza_juridica_id"
            }
             TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }  
    }    
    function onshow()
    {
        parent::show();
    }
    
}