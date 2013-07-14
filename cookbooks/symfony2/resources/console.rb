actions :cmd

attribute :command, :kind_of => String
attribute :path, :kind_of => String
attribute :debug, :default => true
attribute :env, :kind_of => String, :default => "dev"
attribute :optional_params, :kind_of => Array, :default => []