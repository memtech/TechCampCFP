<?php namespace OpenCFP\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class DatabaseServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        try {
            $pdo = $this->makePDOInstance($app);
        } catch (\PDOException $e) {
            $this->raiseDatabaseConnectionIssue();
        }

        $this->checkConnection($pdo);

        $app['db'] = $pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }

    private function checkConnection($pdo)
    {
        $check = $pdo->query('select database() as db')->fetch(\PDO::FETCH_ASSOC);

        if (!$check['db']) {
            $this->raiseDatabaseConnectionIssue();
        }
    }

    /**
     * @param Application $app
     *
     * @return \PDO
     */
    private function makePDOInstance(Application $app)
    {
        return new \PDO(
            $app->config('database.dsn'),
            $app->config('database.user'),
            $app->config('database.password'),
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
    }

    private function raiseDatabaseConnectionIssue()
    {
        throw new \Exception('There was a problem connecting to the database. Make sure to use proper DSN format. See: http://php.net/manual/en/pdo.connections.php');
    }
}
