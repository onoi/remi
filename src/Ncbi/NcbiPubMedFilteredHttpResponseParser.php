<?php

namespace Onoi\Remi\Ncbi;

use Onoi\Remi\FilteredHttpResponseParser;
use Onoi\Remi\FilteredRecord;
use DOMDocument;

/**
 * @license GNU GPL v2+
 * @since 0.1
 *
 * @author mwjames
 */
class NcbiPubMedFilteredHttpResponseParser extends FilteredHttpResponseParser {

	/**
	 * @see http://www.ncbi.nlm.nih.gov/books/NBK25501/
	 * @see http://www.ncbi.nlm.nih.gov/books/NBK1058/
	 */
	const SUMMARY_URL = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?";
	const FETCH_URL = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?";

	/**
	 * @since 0.1
	 *
	 * {@inheritDoc}
	 */
	public function getRawResponse( $id ) {

		$db = $this->getRecord()->get( 'ncbi-dbtype' );

		return $this->requestSummaryResponseFor( $id, $db ) . $this->requestAbstractResponseFor( $id, $db );
	}

	/**
	 * @since 0.1
	 *
	 * {@inheritDoc}
	 */
	public function doParseFor( $id ) {

		$db = $this->getRecord()->get( 'ncbi-dbtype' );

		$text = $this->requestSummaryResponseFor( $id, $db );

		if ( $this->httpRequest->getLastError() !== '' ) {
			return $this->addMessage( array( 'onoi-remi-request-error', $this->httpRequest->getLastError(), $id ) );
		}

		$this->doProcessSummary( $text, $id, $db );

		if ( $this->getMessages() !== array() ) {
			return null;
		}

		$this->doProcessAbstract(
			$this->requestAbstractResponseFor( $id, $db )
		);
	}

	private function doProcessSummary( $text, $id, $db ) {

		$result = json_decode(
			$text,
			true
		);

		if ( !isset( $result['result'][$id] ) ) {
			return $this->addMessage( array( 'onoi-remi-response-empty', $id ) );
		}

		$record = $result['result'][$id];

		if ( isset( $record['error'] ) ) {
			return $this->addMessage( array( 'onoi-remi-request-error', $record['error'], $id ) );
		}

		if ( isset( $record['pubtype'] ) ) {
			foreach ( $record['pubtype'] as $type ) {
				$this->getRecord()->append( 'type', $type );
			}
		}

		foreach ( $record['articleids'] as $articleids ) {

			if ( $articleids['idtype'] === 'doi' ) {
				$this->getRecord()->set( 'doi', $articleids['value'] );
			}

			if ( $articleids['idtype'] === 'pmid' ) {
				$this->getRecord()->set( 'pmid', $articleids['value'] );
			}

			if ( $articleids['idtype'] === 'pubmed' ) {
				$this->getRecord()->set( 'pmid', $articleids['value'] );
			}

			if ( $articleids['idtype'] === 'pmcid' && $db === 'pmc' ) {
				$this->getRecord()->set( 'pmcid', $articleids['value'] );
			}

			if ( $articleids['idtype'] === 'pmc' && $db === 'pubmed' ) {
				$this->getRecord()->set( 'pmcid', $articleids['value'] );
			}
		}

		foreach ( $record['authors'] as $author ) {
			$this->getRecord()->append( 'author', $author['name'] );
		}

		$this->getRecord()->set( 'title', $record['title'] );
		$this->getRecord()->set( 'journal', $record['fulljournalname'] );
		$this->getRecord()->set( 'pubdate', $record['pubdate'] );
		$this->getRecord()->set( 'volume', $record['volume'] );
		$this->getRecord()->set( 'issue', $record['issue'] );
		$this->getRecord()->set( 'pages', $record['pages'] );
	}

	private function doProcessAbstract( $xml ) {

		$ncbiEntrezAbstractXMLProcessor = new NcbiEntrezAbstractXMLProcessor(
			$this->getRecord()
		);

		$ncbiEntrezAbstractXMLProcessor->doProcess( $xml );
	}

	/**
	 * @param string $id
	 *
	 * @return string
	 */
	public function requestSummaryResponseFor( $id, $type ) {

		$this->httpRequest->setOption( CURLOPT_RETURNTRANSFER, true ); // put result into variable
		$this->httpRequest->setOption( CURLOPT_FAILONERROR, true );
		$this->httpRequest->setOption( CURLOPT_URL, self::SUMMARY_URL );

		$this->httpRequest->setOption( CURLOPT_HTTPHEADER, array(
			'Accept: application/sparql-results+json, application/rdf+json, application/json',
			'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
		) );

		$this->httpRequest->setOption( CURLOPT_POST, true );

		$this->httpRequest->setOption(
			CURLOPT_POSTFIELDS,
			"db={$type}&retmode=json&id=" . $id
		);

		return $this->httpRequest->execute();
	}

	/**
	 * @param string $id
	 *
	 * @return string
	 */
	public function requestAbstractResponseFor( $id, $type ) {

		// http://www.fredtrotter.com/2014/11/14/hacking-on-the-pubmed-api/

		$this->httpRequest->setOption( CURLOPT_RETURNTRANSFER, true ); // put result into variable
		$this->httpRequest->setOption( CURLOPT_FAILONERROR, true );
		$this->httpRequest->setOption( CURLOPT_URL, self::FETCH_URL );

		$this->httpRequest->setOption( CURLOPT_HTTPHEADER, array(
			'Accept: application/sparql-results+xml, application/rdf+xml, application/xml',
			'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
		) );

		$this->httpRequest->setOption( CURLOPT_POST, true );

		$this->httpRequest->setOption(
			CURLOPT_POSTFIELDS,
			"db={$type}&rettype=abstract&retmode=xml&id=" . $id
		);

		return $this->httpRequest->execute();
	}

}
