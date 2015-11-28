<?php

namespace FrontModule\Presenters;

use Nette,
	Model;

use Tulinkry;
use Entity\Post;
use Nette\Mail\IMailer;


/**
 * @IgnoreAnnotation ( "Cron" )
 * @Cron
 */
class ImportPresenter extends BasePresenter
{
	/** @inject @var Model\EventModel */
	public $events;

	/** @inject @var Model\PostModel */
	public $posts;

	/** @inject @var Model\FbImportModel */
	public $fb;

	/** @inject @var Model\ParametersModel */
	public $param;

	/**
	 * @inject
	 * @var IMailer 
	 */
	public $mailer;


	public function renderDefault ()
	{
		try
		{
			$this -> importFb ();
		}
		catch ( \Exception $e )
		{
			throw $e;
		}

		$this -> terminate ();
	}

	protected function chooseTheBiggest ( $photo )
	{
		if ( ! array_key_exists ( "images", $photo ) || ! count ( $photo [ "images" ] ) )
			return [ "source" => "" ];
		
		$photos = (array)$photo [ "images" ];

		usort ( $photos, function ( $a, $b ) { return $a["width"] < $b["width" ]; } );

		return $photos [ 0 ];
	}


	protected function importFb ()
	{
		set_time_limit ( 0 );
		
		if ( ! count ( ( $param = $this -> param -> by ( [], [], 1 ) ) ) )
			$this -> param -> insert ( $param = $this -> param -> create ( 
					[ "posts_last_update" => Tulinkry\DateTime::createFromFormat ( "Y-m-d H:i:s", "1970-01-05 00:00:00" ),
					  "events_last_update" => Tulinkry\DateTime::createFromFormat ( "Y-m-d H:i:s", "1970-01-05 00:00:00" ) ] ) );
		else
			$param = $param[0];


		$this -> template -> str = "";
		

		$array = array ();

		$events = $this -> fb -> getAllEvents ( $param -> events_last_update -> getTimestamp () );
		foreach ( $events as $event )
		{
			try{
				$array [] = $e = $this -> events -> import ( $event, Model\EventModel::INSERT );
				$param -> events_last_update = $e -> start > $param -> events_last_update ? $e -> start : $param -> events_last_update;
			} catch ( \Exception $e )
			{}
		}
		$param -> events_last_update -> modify ( "+1 second" );


		if ( count ( $array ) )
		{
			try
			{

				$this -> events -> flush ();
				$this -> param -> update ( $param ); // not necessary

				$params = array ( "events" => $array );

				if ( ! array_key_exists ( "import", $this -> parameters -> params ) ||
					 ! array_key_exists ( "eventsTemplate", $this -> parameters -> params [ "import" ] ) )
					throw new Tulinkry\Exception ( "Config section 'eventsTemplate' is missing in parameters['import']. Path to email template." );

				$this -> sendEmail ( $this -> parameters -> params [ "import" ] [ "eventsTemplate" ], $params, "import událostí" );

			} catch ( \Exception $e )
			{
				throw $e;
			}
			
		}

		$array = array ();

		$posts = $this -> fb -> getAllPosts ( $param -> posts_last_update -> getTimestamp () );
		foreach ( $posts as $post )
		{
			if ( $post [ "type" ] == Post::TYPE_PHOTO )
			{
	            /*
	             * retreive full sized photo
	             */
	            if ( array_key_exists ( "object_id", $post ) )
	            {
	            	$photo = $this -> fb -> item ( $post [ "object_id" ] );
	            	$photo = $this -> chooseTheBiggest ( $photo );
	            	$post [ "picture" ] = $photo [ "source" ];
	            } 				
			}

			//echo "<pre>";
			//print_r ( $post );
			//echo "</pre>";
			try{
				$array [] = $p = $this -> posts -> import ( $post, Model\PostModel::INSERT );
				$param -> posts_last_update = $p -> datum > $param -> posts_last_update ? $p -> datum : $param -> posts_last_update;
			} catch ( \Exception $e )
			{}
		}
		$param -> posts_last_update -> modify ( "+1 second" );


		if ( count ( $array ) )
		{
			try
			{

				$this -> posts -> flush ();
				$this -> param -> update ( $param );

				$params = array ( "posts" => $array );
				
				if ( ! array_key_exists ( "import", $this -> parameters -> params ) ||
					 ! array_key_exists ( "postsTemplate", $this -> parameters -> params [ "import" ] ) )
					throw new Tulinkry\Exception ( "Config section 'postsTemplate' is missing in parameters['import']. Path to email template." );

				$this -> sendEmail ( $this -> parameters -> params [ "import" ] [ "postsTemplate" ], $params, "import přispěvků" );

			} catch ( \Exception $e )
			{
				throw $e;
			}
			
		}
	}


	protected function sendEmail ( $template_file, $params = [], $subject )
	{
		$params = array_merge( $params, [ "presenter" => $this ] );
	
		$latte = new \Latte\Engine;

		$str = $latte -> renderToString ( $template_file, $params );

		if ( ! array_key_exists ( "import", $this -> parameters -> params ) )
			throw new Tulinkry\Exception ( "Config section 'import' is missing in parameters." );
		 $import = $this -> parameters -> params [ "import" ];

		if ( ! array_key_exists ( "to", $import ) )
			throw new Tulinkry\Exception ( "Config section 'to' is missing in parameters['import']." );
		$to = $import [ "to" ];

		if ( ! array_key_exists ( "from", $import ) )
			throw new Tulinkry\Exception ( "Config section 'from' is missing in parameters['import']." );
		$from = $import [ "from" ];

		$subject_template = "Sdělení stránky - %s";
		if ( array_key_exists ( "subject", $import ) &&
			 count ( explode ( "%s", $import [ "subject" ] ) ) === 2 )
			$subject_template = $import [ "subject" ];

		$subject = sprintf ( $subject_template, $subject );
	
		if ( ! is_array ( $to ) )
			$to = array ( $to );

		$message = new \Nette\Mail\Message;
		$message -> setFrom ( $from )
			     -> setSubject ( $subject )
			     -> setHtmlBody ( $str );

		foreach ( $to as $recipient )
			$message-> addTo ( $recipient );

		$this -> mailer -> send ( $message );
		$this -> template -> str .= $str;		
	}




}
