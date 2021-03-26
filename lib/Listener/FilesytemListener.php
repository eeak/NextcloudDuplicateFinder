<?php
namespace OCA\DuplicateFinder\Listener;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\NodeDeletedEvent;
use OCP\Files\Events\Node\NodeRenamedEvent;
use OCP\Files\Events\Node\NodeCopiedEvent;
use OCP\Files\Events\Node\NodeCreatedEvent;
use OCP\Files\Events\Node\NodeWrittenEvent;
use OCP\Files\Events\Node\NodeTouchedEvent;
use OCA\DuplicateFinder\Service\FileInfoService;

class FilesytemListener implements IEventListener {

	/** @var FileInfoService */
	private $fileInfoService;

  public function __construct(FileInfoService $fileInfoService) {
		$this->fileInfoService = $fileInfoService;
	}

  public function handle(Event $event): void {
    if ($event instanceOf NodeDeletedEvent) {
      $this->fileInfoService->delete($event->getNode()->getPath());
    }elseif($event instanceOf NodeRenamedEvent){
      $this->fileInfoService->delete($event->getSource()->getPath());
      $target = $event->getTarget();
      $this->fileInfoService->createOrUpdate($target->getOwner(), $target->getPath());
    }elseif($event instanceOf NodeCopiedEvent
      || $event instanceOf NodeCreatedEvent
      || $event instanceOf NodeWrittenEvent
      || $event instanceOf NodeTouchedEvent){
      $node = $event->getNode();
			$this->fileInfoService->createOrUpdate($node->getOwner(), $node->getPath());
    }
  }
}
