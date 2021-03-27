<?

//Configurações do banco de dados
define('MYSQL_DBLIB', 'mysql');
define('MYSQL_HOST', '127.0.0.1');
define('MYSQL_DBNAME', 'CRUD');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', '');

//PRINT_R PRE
function pr($dado, $print_r = true) {
    echo '<pre>';
    if ($print_r) {
        print_r($dado);
    } else {
        var_dump($dado);
    }
}

//RETORNO - inicio
$retorno = [
    'status' => 'erro',
    'mensagem' => 'Ação não identificada',
    'lista' => [],
    'dado' => ''
];

try {

    //CONEXAO
    $PDO = new PDO(
            MYSQL_DBLIB . ':host=' . MYSQL_HOST . ';dbname=' . MYSQL_DBNAME,
            MYSQL_USERNAME, MYSQL_PASSWORD
    );
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Json para Post
    $_POST = json_decode(file_get_contents('php://input'), true);

    //LISTAR
    if (@$_POST['ACAO'] == 'Listar') {
        $query = $PDO->query('
            SELECT * FROM PESSOA ORDER BY NOME
        ');
        $DADOS = $query->fetchAll(PDO::FETCH_ASSOC);
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Pessoas listadas';
        $retorno['lista'] = $DADOS;
    }
    //INCLUIR
    elseif (@$_POST['ACAO'] == 'Incluir') {
        $prepare = $PDO->prepare('
            INSERT INTO PESSOA (NOME) VALUES (:NOME)
        ');
        $prepare->execute([
            ':NOME' => @$_POST['NOME']
        ]);
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = "$_POST[NOME] incluído(a)";
    }
    //EXCLUIR
    elseif (@$_POST['ACAO'] == 'Excluir') {
        $prepare = $PDO->prepare('
            DELETE FROM PESSOA WHERE ID_PESSOA = :ID_PESSOA
        ');
        $prepare->execute([
            ':ID_PESSOA' => @$_POST['ID_PESSOA']
        ]);
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = "$_POST[descricao] excluído(a)";
    }
    //Consulta
    elseif (@$_POST['ACAO'] == 'Buscar') {
        $prepare = $PDO->prepare('
            SELECT * FROM PESSOA WHERE ID_PESSOA = :ID_PESSOA
        ');
        $prepare->execute([
            ':ID_PESSOA' => @$_POST['ID_PESSOA']
        ]);
        $DADO = $prepare->fetch(PDO::FETCH_ASSOC);
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Pessoa listada';
        $retorno['dado'] = $DADO;
        if (!$DADO) {
            $retorno['status'] = 'erro';
            $retorno['mensagem'] = 'Pessoa não localizada';
        }
    }
    //ALTERAR
    elseif (@$_POST['ACAO'] == 'Alterar') {
        $prepare = $PDO->prepare('
            UPDATE PESSOA SET NOME = :NOME WHERE ID_PESSOA = :ID_PESSOA
        ');
        $prepare->execute([
            ':NOME' => $_POST['NOME'],
            ':ID_PESSOA' => $_POST['ID_PESSOA']
        ]);
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = "$_POST[NOME] alterado(a)";
    }
} catch (Exception $ex) {
    $retorno = [
        'status' => 'erro',
        'mensagem' => $ex->getMessage()
    ];
}

exit(json_encode($retorno));
