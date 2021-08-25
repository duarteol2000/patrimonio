<?php
class GraficoBensPorTipoProd extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Filtro de Bens por Categoria');
        
        $combo_categoria = new TDBCombo('categoria_id', 'patrimonio', 'Categoria', 'id', 'descricao'  );
        $combo_grafico   = new TCombo('combo');
        
        $this->opcoes_grafico = array('1'=>'Barra','2'=>'Colunas','3'=>'Linhas','4'=>'Pizza');
        $combo_grafico->addItems($this->opcoes_grafico); 
        
        $combo_categoria->setChangeAction( new TAction( array($this, 'onChangeAction')) );

        $criteria = new TCriteria;
        $criteria->add(new TFilter('categoria_id','=',$combo_categoria->id));

        $this->form->addFields( [new TLabel('Categoria')], [$combo_categoria] );
        $this->form->addFields( [new TLabel('Tipo de Gráfico')], [$combo_grafico] );  
              
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
            
            //Escolha da Categoria            
            if ($data->categoria_id)
            {
                $criteria->add(new TFilter('categoria_id', '=', $data->categoria_id));
            }
            
            echo'<pre>';
            print ( $param['combo'] );
            echo'</pre>';
            
            $rows = $repository->load($criteria);
             
            if ($rows)
            {
                //$widths = [40, 200, 80, 120, 80];
                //$widths = [800, null, null,null, null];

                //parent::add(new TLabel('Demonstrativos'));
                $table = new TTable;
                        
                parent::add($table);
                
                //Escolha do Tipo de Gráfico
                
                if (!$param['combo']){
                    $html = new THtmlRenderer('app/resources/google_bar_chart.html');
                }
                if ($param['combo'] == 1){
                    $html = new THtmlRenderer('app/resources/google_bar_chart.html');
                }
                if ($param['combo'] == 2){
                    $html = new THtmlRenderer('app/resources/google_column_chart.html');
                }
                if ($param['combo'] == 3){
                    $html = new THtmlRenderer('app/resources/google_line_chart.html');
                }
                if ($param['combo'] == 4){
                    $html = new THtmlRenderer('app/resources/google_pie_chart.html');
                }
                
                TTransaction::open('patrimonio');
                $conn = TTransaction::get();
                
                if ($data->categoria_id)
                {
                    $colunas = $conn->query('SELECT * FROM viewgraficobensportipo where categoria_id = '. $data->categoria_id);
                }else{
                    $colunas = $conn->query('SELECT * FROM viewgraficobensportipo');
                }
                $dados[] = ['Categoria','Valor'];
                
                foreach($colunas as $coluna)
                {
                    $dados[] = [$coluna[0],(float)$coluna[1]]; 
                }
                
                $div = new TElement('div');
                $div->id = 'container';
                $div->style = 'width:100%;height:100%';
                $div->add($html);
                
                $html->enableSection('main', array('data' => json_encode($dados),
                                                   'width'  => '100%',
                                                   'height'  => '300px',
                                                   'title'  => 'Bens por Categora',
                                                   'ytitle' => 'Quantidade',
                                                   'xtitle' => 'Tipo de Bens Patrimoniais'));
                
                //print_r($dados);
                
                TTransaction::close();
                parent::add($div);
                        
                
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