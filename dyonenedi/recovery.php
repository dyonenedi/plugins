<?php
	
	namespace App\Plugin\Dyonenedi;
	
	use Lidiun\Request;
	use App\Plugin\Dyonenedi\Building;
	use App\Plugin\Dyonenedi\Mail;
	use App\Plugin\Dyonenedi\Encrypt;

	Class Recovery
	{
		private $email;
		private $name;
		private $token;
		private $password;
		
		public $errorMessage;
		
		public function __construct($email){
			$this->email = $email;
		}

		public function generateToken(){
			$result = Building::select('*')
				->from('user')
				->where(['email' => $this->email, 'active' => 1])
			->run('array');
			
			if (!empty($result) && !empty($result[0])) {
				$this->name = ucwords($result[0]['name']);
				$this->token = uniqid(rand(), true);

				$result = Building::insert()
					->into('token_recover')
					->values(['token' => $this->token, 'email' => $this->email])
				->run();

				if ($result) {
					return true;
				} else {
					$this->errorMessage = 'Não foi possível criar o token.'.Building::getErrorMessage();
					return false;
				}
			} else {
				$this->errorMessage = 'Esse Email não foi registrado.';
				
				return false;
			}
		}

		public function sendEmail(){
			$from     = 'suporte@seapegue.com.br'; 
			$fromName = 'Se Apegue'; 
			$toName   = $this->name;
			$subject  = 'Recuperação de Senha'; 
			$message  = "Olá ".$this->name.".\n"; 
			$message .= "Segue abaixo o link de recuperação de senha solicitado pelo site Se Apegue. Caso nao tenha solicitado ignore esse Email. \n\n"; 
			$message .= "Link: ".Request::$_url['site']."recovering/".$this->token."/".$this->email; 

			if (Mail::sendMail($from, $this->email, $subject, $message, $fromName, $toName)) {
				return true;
			} else {
				$this->errorMessage = 'Não conseguimos enviar o email pra você. Entre em contato com o nossa equipe de suporte.';

				return false;
			}
		}

		public function validateToken($token, $email){
			$date = date('Y-m-d', strtotime("-2 days"));
			$result = Building::select('id')
				->from('token_recover')
				->where(['token' => $token, 'email' => $email, "date >= '".$date."' AND active" => 1])
			->run('num_rows');

			if (!empty($result)) {
				return true;
			} else {
				$this->errorMessage = 'O Token não é mais válido ou expirou (2 dias). Solicite o link de recuperação novamente.';

				return false;
			}
		}

		public function changePassword($email, $password, $token){
			$result = Building::update('token_recover')
				->set(['active' => 0])
				->where(['token' => $token])
			->run();

			if ($result) {
				$password = Encrypt::encodePassword($password);
				$result = Building::update('user')
					->set(['password' => $password])
					->where(['email' => $email])
				->run();

				if ($result) {
					return true;
				} else {
					$this->errorMessage = 'Não foi possível atualizar a senha.';
					return false;
				}
			} else {
				$this->errorMessage = 'Não foi possível desativar esse token, tente novamente mais tarde.';
				return false;
			}
		}
	}