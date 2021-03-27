<center>
    <h3 id="ACAO_MSG_OK" style="color: green"></h3>
    <h3 id="ACAO_MSG_ERRO" style="color: red"></h3>
    <table border="1" style="min-width: 500px">
        <tr style=" vertical-align: top">
            <td style="text-align: right;">
                <h2 style="text-align: center">Pessoas</h2> 
                <table border="1" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOME</th>
                            <th>AÇÃO</th>
                        </tr>                        
                    </thead>
                    <tbody id="TBODY">
                        <!-- AQUI VEM O CONTEUDO DO AJAX -->
                    </tbody>
                </table>
            </td>
            <td style="text-align: center">
                <h3 id="ACAO_TITULO">Incluir</h3> 
                <form id="FORM" method='POST' onsubmit="return incluir()">
                    <input id="NOME" maxlength="100" required><br>
                    <input id="ACAO" type="submit" value="Salvar">
                </form>
            </td>
        </tr>
    </table>
</center>

<script>

    function listar() {

        var $_POST = {
            ACAO: 'Listar'
        };

        var ajax = new XMLHttpRequest();
        ajax.open('POST', 'ajax.php');
        ajax.onload = function () {
            var $RETORNO = JSON.parse(ajax.responseText);
            if ($RETORNO.status == 'ok') {
                $LISTA = $RETORNO.lista;
                var tr = '';
                if ($LISTA.length > 0) {
                    for (var $dado of $LISTA) {
                        tr += '<tr>';
                        tr += ' <td>' + $dado.ID_PESSOA + '</td>';
                        tr += ' <td>' + $dado.NOME + '</td>';
                        tr += ' <td> ';
                        tr += '  <button onclick="editar(' + $dado.ID_PESSOA + ')">Editar</button>';
                        tr += '  <button descricao="' + $dado.NOME + '" onclick="excluir(' + $dado.ID_PESSOA + ',this)">Excluir</button>';
                        tr += ' </td>';
                        tr += '</tr>';
                    }
                } else {
                    tr += '<tr><td colspan="3" style="text-align: center; color: blue">Sem dados</td></tr>';
                }
                TBODY.innerHTML = tr;
            } else {
                ACAO_MSG_ERRO.textContent = $RETORNO.mensagem;
            }
        }
        ajax.send(JSON.stringify($_POST));
    }

    function incluir() {

        var $_POST = {
            ACAO: 'Incluir',
            NOME: NOME.value
        };

        var ajax = new XMLHttpRequest();
        ajax.open('POST', 'ajax.php');
        ajax.onload = function () {
            var $RETORNO = JSON.parse(ajax.responseText);
            if ($RETORNO.status == 'ok') {

                //Mensagem
                ACAO_MSG_OK.textContent = $RETORNO.mensagem;
                ACAO_MSG_ERRO.textContent = '';

                //Estrutura
                FORM.reset();

                //Listar
                listar();
            } else {
                ACAO_MSG_OK.textContent = '';
                ACAO_MSG_ERRO.textContent = $RETORNO.mensagem;
            }
        }

        ajax.send(JSON.stringify($_POST));
        return false;
    }

    function excluir(id, elemento) {
        var descricao = elemento.getAttribute('descricao');
        if (confirm('Confirma exclusão de ' + descricao + ' ?')) {

            var $_POST = {
                ACAO: 'Excluir',
                ID_PESSOA: id,
                descricao: descricao
            };

            var ajax = new XMLHttpRequest();
            ajax.open('POST', 'ajax.php');
            ajax.onload = function () {
                var $RETORNO = JSON.parse(ajax.responseText);
                if ($RETORNO.status == 'ok') {

                    //Mensagem
                    ACAO_MSG_OK.textContent = $RETORNO.mensagem;
                    ACAO_MSG_ERRO.textContent = '';

                    //Listar
                    listar();
                } else {
                    ACAO_MSG_OK.textContent = '';
                    ACAO_MSG_ERRO.textContent = $RETORNO.mensagem;
                }
            }

            ajax.send(JSON.stringify($_POST));
        }
        return false;
    }

    function editar(id) {

        var $_POST = {
            ACAO: 'Buscar',
            ID_PESSOA: id
        };

        var ajax = new XMLHttpRequest();
        ajax.open('POST', 'ajax.php');
        ajax.onload = function () {
            var $RETORNO = JSON.parse(ajax.responseText);
            var $DADO = $RETORNO.dado;
            if ($RETORNO.status == 'ok') {
                //Dados
                NOME.value = $DADO.NOME;

                //Estrutura
                ACAO_TITULO.innerHTML = 'Alterar'
                FORM.setAttribute('onsubmit', 'return alterar(' + $DADO.ID_PESSOA + ')');

            } else {
                ACAO_MSG_OK.textContent = '';
                ACAO_MSG_ERRO.textContent = $RETORNO.mensagem;
            }
        }

        ajax.send(JSON.stringify($_POST));
        return false;
    }

    function alterar(id) {

        var $_POST = {
            ACAO: 'Alterar',
            ID_PESSOA: id,
            NOME: NOME.value
        };

        var ajax = new XMLHttpRequest();
        ajax.open('POST', 'ajax.php');
        ajax.onload = function () {
            var $RETORNO = JSON.parse(ajax.responseText);
            if ($RETORNO.status == 'ok') {
                FORM.reset();

                //Estrutura
                ACAO_TITULO.innerHTML = 'Incluir'
                FORM.setAttribute('onsubmit', 'return incluir()');

                //Mensagem
                ACAO_MSG_OK.textContent = $RETORNO.mensagem;
                ACAO_MSG_ERRO.textContent = '';

                //Lisar
                listar();
            } else {
                ACAO_MSG_OK.textContent = '';
                ACAO_MSG_ERRO.textContent = $RETORNO.mensagem;
            }
        }

        ajax.send(JSON.stringify($_POST));
        return false;
    }

    //Listar
    listar();
</script>