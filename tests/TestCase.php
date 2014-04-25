<?php



class TestCase extends \Illuminate\Foundation\Testing\TestCase
{

    use \Way\Tests\ModelHelpers;

    public function setUp()
    {
        parent::setUp();
        $this->prepareForTests();
    }

    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting     = true;
        $testEnvironment = 'testing';
        return require __DIR__ . '/../../../../bootstrap/start.php';
    }

    /**
     * Migrates the database and set the mailer to 'pretend'.
     * This will cause the tests to run quickly.
     *
     */
    protected function prepareForTests()
    {
        Artisan::call('migrate', array('--bench' => 'weboap/option'));
        Artisan::call('db:seed', array('--class' => 'Weboap\Option\Seeds\DatabaseSeeder'));
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function test_it_works()
    {
        $this->assertTrue(true);
    }
}
