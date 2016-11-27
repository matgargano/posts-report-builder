<?php

use Cafemedia\Ingest\Upload;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class UploadTest extends TestCase
{

    private $UploadedFile;
    private $Model;
    private $Request;
    private $Converter;


    public function setUp()
    {

        parent::setUp();
        $this->UploadedFile = $this->getMockBuilder('Illuminate\Http\UploadedFile')
                                   ->disableOriginalConstructor()
                                   ->getMock();
        $this->Model        = $this->getMockBuilder('Illuminate\Database\Eloquent\Model')
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->Request   = $this->getMockBuilder('Illuminate\Http\Request')
                                ->disableOriginalConstructor()
                                ->getMock();
        $this->Converter = $this->getMockBuilder('Cafemedia\Convert\Converter')
                                ->disableOriginalConstructor()
                                ->getMock();


    }


    /**
     * @covers \Cafemedia\Ingest\Upload::checkMimeType()
     */
    public function testCheckMimeTypeTrue()
    {

        $value = $this->getMimeTypeHelper('foo', 'foo');
        $this->assertTrue($value);


    }

    /**
     * @covers \Cafemedia\Ingest\Upload::checkMimeType()
     */
    public function testCheckMimeTypeFalse()
    {

        $value = $this->getMimeTypeHelper('not', 'the.same');
        $this->assertFalse($value);


    }

    /**
     * @covers \Cafemedia\Ingest\Upload::getFileContents()
     */
    public function testGetFileContentsFile()
    {

        $pass   = 'foo';
        $upload = new Upload($this->UploadedFile, $this->Request, $this->Model, [], $this->Converter);
        File::shouldReceive('get')
            ->once()
            ->with($this->UploadedFile)
            ->andReturn($pass);

        $method = self::getMethod('getFileContents');

        $contents = $method->invoke($upload);

        $this->assertEquals($pass, $contents);


    }


    /**
     * @covers \Cafemedia\Ingest\Upload::getFileContents()
     */
    public function testGetFileContentsException()
    {

        $upload = new Upload($this->UploadedFile, $this->Request, $this->Model, [], $this->Converter);
        File::shouldReceive('get')
            ->once()
            ->with($this->UploadedFile)
            ->andThrow(FileNotFoundException::class);

        $method = self::getMethod('getFileContents');

        $contents = $method->invoke($upload);

        $this->assertFalse($contents);

    }

    /**
     * @covers \Cafemedia\Ingest\Upload::convertData();
     */
    public function testConvertDataSet()
    {
        $returnValue = 'The Quick';
        $upload = new Upload($this->UploadedFile, $this->Request, $this->Model, [], $this->Converter);

        $this->Converter->method('getConvertedData')
                        ->willReturn($returnValue);
        $method = self::getMethod('convertData');
        $method->invoke($upload);
        $value = self::getProperty($upload, 'dataConverted');
        $this->assertEquals($value, $returnValue);

    }

    /**
     * @covers \Cafemedia\Ingest\Upload::ingest()
     */
    public function testIngestBadMimeType(){

        $expectedReturn = [
            'success' => false,
            'messages' => array(Upload::INVALID_MIME_TYPE_MESSAGE)
        ];

        $this->UploadedFile->method('getMimeType')
                           ->willReturn(123);

        $upload = new Upload($this->UploadedFile, $this->Request, $this->Model, [233], $this->Converter);
        $ingestReturn = $upload->ingest();

        $this->assertEquals($expectedReturn, $ingestReturn);

    }

    /**
     * @covers \Cafemedia\Ingest\Upload::ingest()
     */
    public function testIngestBadContents(){

        $expectedReturn = [
            'success' => false,
            'messages' => array(Upload::FALLBACK_ERROR_MESSAGE)
        ];

        $this->UploadedFile->method('getMimeType')
                           ->willReturn(123);

        $upload = new Upload($this->UploadedFile, $this->Request, $this->Model, [123], $this->Converter);
        File::shouldReceive('get')
            ->once()
            ->with($this->UploadedFile)
            ->andThrow(FileNotFoundException::class);

        $ingestReturn = $upload->ingest();

        $this->assertEquals($expectedReturn, $ingestReturn);

    }

    /**
     * @covers \Cafemedia\Ingest\Upload::ingest()
     */
    public function testIngestBadBadData(){

        $expectedReturn = [
            'success' => false,
            'messages' => array(Upload::FALLBACK_ERROR_MESSAGE)
        ];

        $this->UploadedFile->method('getMimeType')
                           ->willReturn(123);

        $upload = new Upload($this->UploadedFile, $this->Request, $this->Model, [123], $this->Converter);
        File::shouldReceive('get')
            ->once()
            ->with($this->UploadedFile)
            ->andReturn([]);

        $ingestReturn = $upload->ingest();

        $this->assertEquals($expectedReturn, $ingestReturn);

    }


    protected function getMimeTypeHelper($returnValue, $passValue)
    {

        $this->UploadedFile->method('getMimeType')
                           ->willReturn($returnValue);

        $upload = new Upload($this->UploadedFile, $this->Request, $this->Model, [$passValue], $this->Converter);
        $upload->ingest();
        $method = self::getMethod('checkMimeType');

        return $method->invoke($upload);


    }


    protected static function getMethod($name)
    {
        $class  = new ReflectionClass('Cafemedia\Ingest\Upload');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    public static function getProperty($object, $property)
    {


        $reflectionClass = new ReflectionObject($object);
        $property        = $reflectionClass->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);

    }
}
