<?

/**
* 
*/
class AuthHelper
{

	private $_redir;
	private $_tableName;
	private $_userColumn		= 'login';
	private $_senhaColumn		= 'senha';
	private $_user;
	private $_senha;
	private $_controllerLogado 	= 'admin';
	private $_actionLogado 		= 'index';
	private $_controllerErro    = 'admin';
	private $_actionErro 		= 'login';


	//GETTER AND SETTER
	public function __set($atrib, $value){  $this->$atrib = $value; }
	public function __get($atrib){ return $value; }

	function __construct() 
	{
		//CRIA O OBJETO QUE AUXILIA O REDIRECIONAMENTO
		$this->_redir = new RedirectHelper();
	}

		


	/*
	 * Método para fazer o login do usuário
	 */

	public function login()
	{

		//INICIO O MODELO DO BANCO
		$db = new Model();
		
		//PASSO A TABELA QUE ESTÁ OS DADOS PARA LOGIN
		$db->_tabela = $this->_tableName;
		
		//DEFINO A STRING DE COMPARAÇÃO
		$where = $this->_userColumn . " = '" . $this->_user . "' AND " . $this->_senhaColumn . " = '" . hash('sha512', $this->_senha) . "'";
		//FAZ A CONSULTA
		$sql = $db -> readLine($where);

		//SE EXISTIR ELE LOGA E CRIA A SESSÃO
		if(count($sql) > 0 AND !empty($sql) )
		{
			$_SESSION['user'] = $sql[$this->_userColumn];
			$_SESSION['dados_usuario'] = $sql;
			$_SESSION['logado'] = 1;
			$_SESSION['hash'] = sha1(DATABASE_CONFIG::$default['banco']);

			return true;
			//$controller = $this->_controllerLogado;
			//$action 	= $this->_actionLogado;
		}
		else //SE NÃO EXISTIR REDIRECIONA PARA A PAGINA DE LOGIN NOVAMENTE
		{
			return false;
			//$controller = $this->_controllerErro;
			//$action 	= $this->_actionErro;
		}

		//FAZ O REDIRECIONAMENTO
		//$this->_redir->goToControllerAction($controller, $action);
	}



	public function logout()
	{
		$_SESSION = array(); 			 
		// Destroi a Sessão
		session_destroy();
		// Modifica o ID da Sessão
		session_regenerate_id();
		// redireciona
		$this->_redir->goToControllerAction($this->_controllerErro, $this->_actionErro);
	}


	public function verificaLogin()
	{
		if(isset($_SESSION['user']) AND isset($_SESSION['dados_usuario']) AND $_SESSION['logado'] == 1 AND $_SESSION['hash'] == sha1(DATABASE_CONFIG::$default['banco']))
		{
			return true;
		}
		else
		{
			session_destroy();
			return false;
		}
	}



	public function codifica ( $string )
	{
		
	  $cipher     = "rijndael-128";
	  $mode       = "cbc";
	  $plain_text = $string;
	  $secret_key = "A#E%]Ru1,sDq";
	  $iv         = "fedcba9876543210";

	  $td = mcrypt_module_open($cipher, "", $mode, $iv);
	  mcrypt_generic_init($td, $secret_key, $iv);
	  $cyper_text = mcrypt_generic($td, $plain_text);
	  
	  //transforma o binario em hexadecimal
	  return bin2hex($cyper_text);	
	}

	//funcao que transforma hexadecimal para binario
	public function hex2bin( $hexdata )
	{
	  $bindata="";

	  for ($i=0;$i<strlen($hexdata);$i+=2) {
	   $bindata.=chr(hexdec(substr($hexdata,$i,2)));
	  }

	  return $bindata;
	}

	//funcao para decriptografar
	public function decodifica ( $string )
	{
		$cipher     = "rijndael-128";
		$mode       = "cbc";
		$secret_key = "A#E%]Ru1,sDq";
		$iv         = "fedcba9876543210";

		$td = mcrypt_module_open($cipher, "", $mode, $iv);

		mcrypt_generic_init($td, $secret_key, $iv);

		return $valor_decodificado = trim(mdecrypt_generic($td, $this->hex2bin($string)));
	}


}

?>
