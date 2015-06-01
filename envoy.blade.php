@servers(['prod' => 'cfp.techcampmemphis.org@104.237.128.47'])

@task('deploy:prod', ['on' => 'prod'])
cd ~/TechCampCFP
git pull origin master
cp ~/configs/production.yml ~/TechCampCFP/config/production.yml
cp ~/configs/phinx.yml ~/TechCampCFP/phinx.yml
#composer update
~/TechCampCFP/vendor/bin/phinx migrate --environment=production
@endtask
