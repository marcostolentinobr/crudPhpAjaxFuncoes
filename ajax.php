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

function listar($id = '', $NOME = '') {
    global $PDO;

    $WHERE = [];
    $EXECUTE = [];
    if ($id) {
        $WHERE[] = ' ID_PESSOA = :ID_PESSOA ';
        $EXECUTE[':ID_PESSOA'] = $id;
    }
    if ($NOME) {
        $WHERE[] = ' NOME = :NOME ';
        $EXECUTE[':NOME'] = $NOME;
    }
    $where = '';
    if ($WHERE) {
        $where = ' WHERE ' . implode(' AND ', $WHERE);
    }

    $sql = "SELECT * FROM PESSOA $where ORDER BY NOME";
    $prepare = $PDO->prepare($sql);
    $prepare->execute($EXECUTE);

    $DADOS = $prepare->fetchAll(PDO::FETCH_ASSOC);
    return $DADOS;
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
        $DADOS = listar();
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Pessoas listadas';
        $retorno['lista'] = @$DADOS;
    }
    //INCLUIR
    elseif (@$_POST['ACAO'] == 'Incluir') {
        $prepare = $PDO->prepare('
            INSERT INTO PESSOA (NOME) VALUES (:NOME)
        ');
        $DADO = @listar('', @$_POST['NOME'])[0];
        if ($DADO) {
            $retorno['status'] = 'erro';
            $retorno['mensagem'] = "$_POST[NOME] já existe";
        } else {
            $prepare->execute([
                ':NOME' => @$_POST['NOME']
            ]);
            $retorno['status'] = 'ok';
            $retorno['mensagem'] = "$_POST[NOME] incluído(a)";
        }
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
        $DADO = listar(@$_POST['ID_PESSOA']);
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Pessoa listada';
        $retorno['dado'] = $DADO[0];
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
