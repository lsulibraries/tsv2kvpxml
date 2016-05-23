# Description
A simple, extensible utility that transforms delimited files to an arbitrary XML format whose elements are based on the column names in the input file(s).

## Usage

### CLI Mode
Can be used on the command line for one-off transformations:

`./d2x input/input.txt`

...where `input/input.txt` is the Tab-delimited input file.

An optional secontd parameter can be supplied to specify the delimiter (the default is tab "\t"). Choices are `tab` `comma` `pipe`

`./d2x input/input.txt tab`

### .ini Mode

Specify an ini file to customize the behavior:

`./d2x --ini=config.ini`

in this example, the options defined in config.ini specify the DirectoryProcessor and an input directory containing delimited files:

	[options]
	processorClass = DirectoryProcessor
	input_dir = input/
	output_dir = output/
	delimiter = tab

NB: at the moment, the `output_dir` option is required, but does nothing for the given `processorClass`.


## Extensibility

The two main base classes at the moment are the `DirectoryProcessor` and `Delim2xml`, where the former describes how to work over a directory and what to do with any output, and the latter defines how the delimited data is manipulated. The following .ini demonstrates this with a project-specific example:


	[options]
	processorClass = MIKDirectoryProcessor
	Delim2xmlClass = MIKDelim2xml
	input_dir      = cdm-mik/input
	output_dir     = cdm-mik/output
	mappings_dir   = cdm-mik/mappings
	delimiter      = tab
	

## todo / roadmap

* add composer autoload
* repo files organization
* document ini settings
* move field/element mappings to ini
* create output directories when they don't exist
