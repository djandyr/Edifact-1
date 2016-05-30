<?php 

namespace Proengeno\Edifact\Message;

use Proengeno\Edifact\EdifactFile;
use Proengeno\Edifact\EdifactRegistrar;
use Proengeno\Edifact\Message\Delimiter;
use Proengeno\Edifact\Validation\MessageValidator;
use Proengeno\Edifact\Exceptions\ValidationException;
use Proengeno\Edifact\Interfaces\EdifactMessageInterface;
use Proengeno\Edifact\Interfaces\MessageValidatorInterface;

abstract class Message implements EdifactMessageInterface
{
    const UNA_SEGMENT = 'UNA';

    private $file;
    private $delimiter;
    private $validator;
    private $currentSegment;
    
    public function __construct(EdifactFile $file, MessageValidatorInterface $validator = null)
    {
        $this->file = $file;
        $this->validator = $validator ?: new MessageValidator;
    }
    
    public static function fromString($string)
    {
        $tmpnam = tempnam(sys_get_temp_dir(), 'edifact');
        $file = new EdifactFile($tmpnam, 'w+');
        $file->writeAndRewind($string);
        return new static($file);
    }

    public static function build($from, $to)
    {
        if (!isset(static::$builderClass)) {
            throw new ValidationException("No static variable $builderClass definied!");
        }
        return new static::$builderClass($from, $to);
    }
    
    public function __toString()
    {
        return $this->file->__toString();
    }

    public function getFilepath()
    {
        return $this->file->getRealPath();
    }

    public function getCurrentSegment()
    {
        return $this->currentSegment;
    }
    
    public function getNextSegment()
    {
        $segment = $this->file->getSegment();

        if ($segment !== false) {
            $segment = $this->currentSegment = $this->getSegmentObject($segment);
        } 
        return $segment;
    }

    public function findNextSegment($searchSegment, $fromStart = false)
    {
        if ($fromStart) {
            $this->file->rewind();
        }

        $searchObject = EdifactRegistrar::getSegment($searchSegment);
        while ($segmentObject = $this->getNextSegment()) {
            if ($segmentObject instanceof $searchObject) {
                return $segmentObject;
            }
        }

        return false;
    }

    public function getPointerPosition()
    {
        return $this->file->tell();
    }

    public function setPointerPosition($position)
    {
        return $this->file->seek($position);
    }

    public function getValidationBlueprint()
    {
        return static::$validationBlueprint;
    }

    public function validate()
    {
        $this->file->rewind();
        $this->validator->validate($this);
        $this->file->rewind();

        return $this;
    }

    public function getDelimiter()
    {
        return $this->file->getDelimiter();
    }
    
    private function getSegmentObject($segLine)
    {
        return call_user_func_array(EdifactRegistrar::getSegment($this->getSegname($segLine)) . '::fromSegLine', [$segLine, $this->getDelimiter()]);
    }

    private function getSegname($segLine) 
    {
        return substr($segLine, 0, 3);
    }
}
