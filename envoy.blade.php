@servers(['prod' => 'cfp.techcampmemphis.org@104.237.128.47'])

@task('deploy:prod', ['on' => 'prod'])
cd ~/TechCampCFP
./deploy.sh
@endtask
