<?php

class ContentTree {
	private $database;
	
	public function __construct(Database $database) {
		if(is_null($database) || !get_class($database) == 'mysqli') {
			throw new Exception('Bad database object');
		}
		
		$this->database = $database;
	}
	
	public function addArticle($contents = '') {
		$blankBlobId = sha1('');
		$newBlobId = sha1($contents);
		$newArticleId = sha1($blankBlobId . $contents);
		
		$this->database->query("insert into blobs values('$newBlobId', '$blankBlobId', null, true, false, false, false, 0, 1, '$contents');");
		$this->database->query("insert into articles values('$newArticleId', '$newBlobId', '$newBlobId');");
	}
	
	public function updateArticle($parentId, $contents = '') {
		$newBlobId = sha1($contents);
		$newArticleId = sha1($parentId . $contents);
		
		$this->database->query("insert into blobs values('$newBlobId', '$parentId', null, true, false, false, false, 0, 1, '$contents');");
		$this->database->query("insert into articles values('$newArticleId', '$newBlobId', '$newBlobId');");
		
		$this->database->query("update blobs set tip = false, content_references = content_references + 1 where sha = '$parentId';");
	}
	
}