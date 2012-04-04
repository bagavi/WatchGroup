
<?php
/**
 * 
 * @author vivekkumarbagaria <vivekee047@gmail.com>
 * 
 * Function : To Edit the groups 
 *
 */
class SpecialEditWatchGroup extends SpecialPage {

	protected $output ;
	protected $user ;
	protected $request ;
	
	public function __construct(){
		parent::__construct( 'EditWatchGroup' );
	}

	public function execute($mode){

		/**
		 *	Check if user is anonymous?
		 *	If User is Anon return with a msg displaying to login
		 */
		$this->user 	= $this->getUser() ;
		if( $this->user->isAnon() ) {
			SpecialWatchGroup::userIsAnon() ;
			return ;
		}
		
		$this->request = $this->getRequest() ;
		$this->output = $this->getOutput() ;
		$this->setHeaders();
		$this->outputHeader();
		$list = SpecialWatchGroup::ExtractWatchGroup($this->user);
		$this->CreateEditForm($list) ;
		}

	/**
	 *	This function will be called when the submit button of thr form is pressed	
	 * 
	 *	To display pages with checkbox next to them.
	 *	Delete all the groups marked with the checkbox
	 */
	public function Edit(){
	/*
	 * Extract pages checked from the form
	 * 
	 * Remove those pages from the Table:watchgroup
	 * Add the deleted pages to Table:watchgroup_deleted 
	 */

		//Display the success(or error) msg
		//Display the groups which have been created/deleted
	}
	
	public function CreateEditForm($list){
		
		$form	 = Xml::openElement( 'form', array( 
											'method'	=> 'post',
											'action'	=> $this->getTitle()->getLocalUrl(),
											'id'		=> 'mw-watchgroup-edit-form' )) ;
		$count = 1;		
		foreach ($list as $page) {
			$title = Title::newFromText($page) ;
			$linkedtitle = Linker::linkKnown(
				SpecialPage::getTitleFor( "WatchParticularGroup/$page" ),$page );
			$form	.= Xml::checkLabel($title, $page, "", array()).'<br>' ;
		}
		$form 	.= Xml::submitButton('Submit' ) ;
		$form 	.= Xml::closeElement( 'form' ) ;
		$this->output->addHTML($form) ;
	}
}
