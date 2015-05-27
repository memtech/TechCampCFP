@servers(['prod' => 'cfp.techcampmemphis.org@104.237.128.47'])

@task('deploy:prod', ['on' => 'prod'])
cd /home/cfp.techcampmemphis.org
./deploy.sh
@endtask
