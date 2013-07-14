#
# Cookbook Name:: camdram
# Recipe:: default
#

include_recipe "apt"

%w{vim git-core}.each do |pkg|
  package pkg
end

include_recipe "camdram::server"
include_recipe "camdram::database"
include_recipe "camdram::symfony"
include_recipe "camdram::sphinx"
include_recipe "camdram::insert-data"
