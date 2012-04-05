<?php
/**
 *
 * @author vivekkumarbagaria
 *
 */
class WatchGroupAction extends WatchAction {

	protected $user, $title, $groupname , $request ;
		public function getName() {
		return 'watchgroup';
	}


	protected function getDescription() {
		return wfMsgHtml( 'addtowatchgroup' );
	}


	/**
	 * This can be either formed or formless depending on the session token given
	 */
	public function show() {
		$this->user = $this->getUser();
		$this->checkCanExecute( $this->user );
		$this->request = $this->getRequest() ;
		$this->groupname = $this->request->getText( "groupname" ) ;
		$this->title = $this->getTitle() ;
		$this->setHeaders();
		// Yet to take care of token and related stuff
		$this->onSubmit( array() );
		$this->onSuccess();
}

	public function onSubmit( $data ) {

		wfProfileIn( __METHOD__ );
		self::doWatchGroup( $this->title, $this->user , $this->groupname );
		wfProfileOut( __METHOD__ );
		return true;
	}

	public static function doWatchGroup( Title $title, User $user , $GroupName ) {
		$watchgroupitem = WatchedGroupItem::fromUserTitleGroupname( $user, $title, $GroupName ) ;
		// $page = WikiPage::factory( $title );
		$watchgroupitem->addWatchGroupPage() ;
		return true;
	}

	public static function doUnwatchGroup( Title $title, User $user , $GroupName ) {
		$watchgroupitem = WatchedGroupItem::fromUserTitleGroupname( $user, $title, $GroupName ) ;
		$watchgroupitem->removeWatchGroupPage();
		return true;
	}


	protected function alterForm( HTMLForm $form ) {
		$form->setSubmitText( wfMsg( 'confirm-watch-button' ) );
	}

	protected function preText() {
		return wfMessage( 'confirm-watch-top' )->parse();
	}

	public function onSuccess() {
		$this->getOutput()->addHTML( 'This page has been added to the given groupname ' );
	}
}


class UnwatchGroupAction extends WatchGroupAction {

	public function getName() {
		return 'unwatchgroup';
	}

	protected function getDescription() {
		return wfMsg( 'removefromwatchgroup' );
	}

	public function onSubmit( $data ) {
		wfProfileIn( __METHOD__ );
		self::doUnwatchgroup( $this->title, $this->user , $this->groupname );
		wfProfileOut( __METHOD__ );
		return true;
	}

	protected function alterForm( HTMLForm $form ) {
		$form->setSubmitText( wfMsg( 'confirm-unwatch-button' ) );
	}

	protected function preText() {
		return wfMessage( 'confirm-unwatch-top' )->parse();
	}

	public function onSuccess() {
		$this->getOutput()->addWikiMsg( 'removed this page from the watchgroupname ' ) ;
	}
}