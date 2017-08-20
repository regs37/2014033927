-------------------------
Usage of this framework
-------------------------

[Warning!]
Other directories except listed below MUST not be
modified to avoid unexpected internal errors.

	[Controller][Directory]
		User Defined controllers must be inside this directory.

	[Model][Directory]
		- User Defined models must be inside this directory.
		- Each Model is automatically loaded in the project, therefore
			there is no need for including into other files.
		- Required Methods & Structure
			- Each Model must be extended to [Query][Class] for
				easier making queries.
			- Each attributes must be in a public and constant for 
				easier access during queries

	[View][Directory]
		- User Defined Templates and User Interfaces must be inside
			in this directory
			
[Routes][File]
	User defined routing must be in the [routes.php] file.