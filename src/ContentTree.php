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
		$timestamp = date('Y-m-d H:i:s');
		
		$blankBlobId = sha1('');
		$newBlobId = sha1($contents . $timestamp);
		$newArticleId = sha1($newBlobId . $contents . $timestamp);
		
		$this->database->query("insert into blobs values('$newBlobId', '$blankBlobId', null, true, false, false, false, '$timestamp', 0, 2, '$contents');");
		$this->database->query("insert into articles values('$newArticleId', '$timestamp', '$newBlobId', '$newBlobId');");
	}
	
	public function updateArticle($articleId, $contents = '') {
		$timestamp = date('Y-m-d H:i:s');
		$parentBlobId = '';
		
		$this->database->query("select content from articles where sha='$articleId';");
		
		if(!$this->database->isResultAvailable()) {
			throw new Exception('Article not found');
		} else {
			$blob = $this->database->fetchRow();
			$parentBlobId = $blob['content'];
		}
		
		$newBlobId = sha1($contents . $timestamp);
		$newArticleId = sha1($newBlobId . $contents . $timestamp);
		
		$this->database->query("insert into blobs values('$newBlobId', '$parentBlobId', null, true, false, false, false, '$timestamp', 0, 2, '$contents');");
		$this->database->query("insert into articles values('$newArticleId', '$timestamp', '$newBlobId', '$newBlobId');");
		
		$this->database->query("update blobs set tip = false, content_references = content_references + 1 where sha = '$parentBlobId';");
	}
	
	public function traverse($articleId) {
		$blobId = '';
		
		$this->database->query("select content from articles where sha='$articleId';");
		
		if(!$this->database->isResultAvailable()) {
			throw new Exception('Article not found');
		} else {
			$blob = $this->database->fetchRow();
			$blobId = $blob['content'];
		}
		
		
	}
	
}