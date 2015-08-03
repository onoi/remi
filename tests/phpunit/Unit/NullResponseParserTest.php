<?php

namespace Onoi\Remi\Tests;

use Onoi\Remi\NullResponseParser;

/**
 * @covers \Onoi\Remi\NullResponseParser
 * @group onoi-remi
 *
 * @license GNU GPL v2+
 * @since   0.1
 *
 * @author mwjames
 */
class NullResponseParserTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$filteredRecord = $this->getMockBuilder( '\Onoi\Remi\FilteredRecord' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\Onoi\Remi\NullResponseParser',
			new NullResponseParser( $filteredRecord )
		);
	}

	public function testCommonMethods() {

		$filteredRecord = $this->getMockBuilder( '\Onoi\Remi\FilteredRecord' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new NullResponseParser( $filteredRecord );

		$this->assertEquals(
			$filteredRecord,
			$instance->getRecord()
		);

		$this->assertFalse(
			$instance->usedCache()
		);

		$this->assertEmpty(
			$instance->getMessages()
		);

		$this->assertNull(
			$instance->doParseFor( 42 )
		);

		$this->assertEmpty(
			$instance->getRawResponse( 42 )
		);
	}

}
