<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Tools;

use Merkushin\Wpal\Tools\Cli\Application;
use Merkushin\Wpal\Tools\Cli\Output;
use Merkushin\Wpal\Tools\Command\Command;
use Merkushin\Wpal\Tools\Workspace;
use PHPUnit\Framework\TestCase;

/**
 * Runs the commands end to end against a throwaway project built from the fixtures.
 *
 * @covers \Merkushin\Wpal\Tools\Cli\Application
 * @covers \Merkushin\Wpal\Tools\Cli\Input
 * @covers \Merkushin\Wpal\Tools\Cli\Output
 * @covers \Merkushin\Wpal\Tools\Command\SnapshotCommand
 * @covers \Merkushin\Wpal\Tools\Command\CheckCommand
 * @covers \Merkushin\Wpal\Tools\Command\CoverageCommand
 * @covers \Merkushin\Wpal\Tools\Command\DiffCommand
 * @covers \Merkushin\Wpal\Tools\Workspace
 */
class ApplicationTest extends TestCase
{
	private string $root;

	protected function setUp(): void
	{
		$this->root = sys_get_temp_dir() . '/wpal-project-' . uniqid();
		mkdir( $this->root . '/src/Service', 0777, true );
		copy( __DIR__ . '/fixtures/service/Hooks.php', $this->root . '/src/Service/Hooks.php' );
	}

	protected function tearDown(): void
	{
		exec( 'rm -rf ' . escapeshellarg( $this->root ) );
	}

	public function testSnapshot_WhenGivenSource_WritesCurrentSnapshot(): void
	{
		[ $code, $out ] = $this->wpal( 'snapshot', '--source=' . __DIR__ . '/fixtures/wordpress' );

		self::assertSame( Command::OK, $code );
		self::assertStringContainsString( 'WordPress 9.9.1', $out );
		self::assertFileExists( $this->root . '/api/wordpress.json' );
	}

	public function testCheck_WhenMethodsDrift_ExitsWithFoundAndReportsJson(): void
	{
		$this->wpal( 'snapshot', '--source=' . __DIR__ . '/fixtures/wordpress' );

		[ $code, $out ] = $this->wpal( 'check', '--format=json' );
		$data           = json_decode( $out, true );

		self::assertSame( Command::FOUND, $code );
		self::assertSame( '9.9.1', $data['wordpress'] );
		self::assertSame( 7, $data['methods'] );
		self::assertSame( 5, $data['drifting'] );
		self::assertSame( 'src/Service/Hooks.php', $data['drift'][0]['file'] );
	}

	public function testCheck_WhenNothingDrifts_ExitsOk(): void
	{
		$this->wpal( 'snapshot', '--source=' . __DIR__ . '/fixtures/wordpress' );
		file_put_contents(
			$this->root . '/src/Service/Hooks.php',
			"<?php\nnamespace Merkushin\\Wpal\\Service;\ninterface Hooks {\n\tpublic function get_thing( int \$id, \$output = 'OBJECT', array \$args = [], &\$found = null );\n}\n"
		);

		[ $code, $out ] = $this->wpal( 'check' );

		self::assertSame( Command::OK, $code );
		self::assertStringContainsString( 'All 1 Service methods match WordPress 9.9.1.', $out );
	}

	public function testCoverage_WhenJson_ReportsGroups(): void
	{
		$this->wpal( 'snapshot', '--source=' . __DIR__ . '/fixtures/wordpress' );

		[ $code, $out ] = $this->wpal( 'coverage', '--format=json' );
		$data           = json_decode( $out, true );

		// The fixture service wraps functions the map doesn't list, and one WordPress doesn't have.
		self::assertSame( Command::FOUND, $code );
		self::assertArrayHasKey( 'Hooks', $data['wrapped'] );
		self::assertArrayHasKey( 'private', $data['ignored'] );
		self::assertContains( 'Hooks::gone_function() wraps a function WordPress 9.9.1 doesn\'t have.', $data['problems'] );
	}

	public function testCoverage_WhenFailOnUntriaged_ExitsFoundForUntriagedFunctions(): void
	{
		$this->wpal( 'snapshot', '--source=' . __DIR__ . '/fixtures/wordpress' );
		file_put_contents( $this->root . '/src/Service/Hooks.php', "<?php\nnamespace Merkushin\\Wpal\\Service;\ninterface Hooks {}\n" );

		[ $lenient ] = $this->wpal( 'coverage' );
		[ $strict ]  = $this->wpal( 'coverage', '--fail-on-untriaged' );

		self::assertSame( Command::OK, $lenient );
		self::assertSame( Command::FOUND, $strict );
	}

	public function testFix_WhenMapChanges_RegeneratesAndCheckDetectsStaleFiles(): void
	{
		$this->wpal( 'snapshot', '--source=' . __DIR__ . '/fixtures/wordpress' );
		file_put_contents( $this->root . '/wpal.map.php', "<?php\nreturn [ 'services' => [ 'Things' => [ 'get_thing', 'do_action' ] ] ];\n" );

		[ $check ]      = $this->wpal( 'fix', '--check' );
		[ $code, $out ] = $this->wpal( 'fix' );
		[ $recheck ]    = $this->wpal( 'fix', '--check' );

		self::assertSame( Command::FOUND, $check );
		self::assertSame( Command::OK, $code );
		self::assertStringContainsString( 'created  src/Service/Things.php', $out );
		self::assertFileExists( $this->root . '/src/Service/WpThings.php' );
		self::assertFileExists( $this->root . '/src/ServiceFactory.php' );
		self::assertSame( Command::OK, $recheck );
	}

	public function testDiff_WhenComparingSnapshotFiles_ListsChanges(): void
	{
		$this->wpal( 'snapshot', '--source=' . __DIR__ . '/fixtures/wordpress' );

		[ $code, $out ] = $this->wpal( 'diff', $this->root . '/api/wordpress.json', 'current', '--format=json' );
		$data           = json_decode( $out, true );

		self::assertSame( Command::OK, $code );
		self::assertSame( [], $data['added'] );
		self::assertSame( [], $data['changed'] );
	}

	public function testRun_WhenCommandUnknown_ExitsWithError(): void
	{
		[ $code, , $err ] = $this->wpal( 'nope' );

		self::assertSame( Command::ERROR, $code );
		self::assertStringContainsString( 'Unknown command "nope"', $err );
	}

	public function testRun_WhenOptionUnknown_ExitsWithError(): void
	{
		[ $code, , $err ] = $this->wpal( 'check', '--bogus' );

		self::assertSame( Command::ERROR, $code );
		self::assertStringContainsString( 'Unknown option --bogus', $err );
	}

	/**
	 * @return array{0: int, 1: string, 2: string} Exit code, stdout, stderr.
	 */
	private function wpal( string ...$args ): array
	{
		$out = fopen( 'php://memory', 'w+' );
		$err = fopen( 'php://memory', 'w+' );
		self::assertNotFalse( $out );
		self::assertNotFalse( $err );

		$code = ( new Application( new Workspace( $this->root ), new Output( $out ), $err ) )->run( array_merge( [ 'wpal' ], $args ) );

		rewind( $out );
		rewind( $err );

		return [ $code, (string) stream_get_contents( $out ), (string) stream_get_contents( $err ) ];
	}
}
