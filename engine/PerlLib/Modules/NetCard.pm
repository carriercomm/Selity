#!/usr/bin/perl

## Selity - When virtual hosting becomes scalable
# Copyright 2012-2014 by Selity
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
#
# @category		Selity
# @copyright	2012 by Selity | http://selity.org
# @author		Daniel Andreca <sci2tech@gmail.com>
# @link			http://selity.org Selity Home Site
# @license		http://www.gnu.org/licenses/gpl-2.0.html GPL v2

package Modules::NetCard;

use strict;
use warnings;
use Selity::Debug;
use Selity::Execute;
use Data::Dumper;

use vars qw/@ISA/;

@ISA = ('Common::SimpleClass');
use Common::SimpleClass;

sub process{

	my $self	= shift;
	my $rs		= 0;
	my ($stdour, $stderr);
	$rs |= execute("$main::selityConfig{ENGINE_ROOT_DIR}/tools/selity-net-interfaces-mngr stop", \$stdour, \$stderr);
	debug($stdour) if $stdour;
	error($stderr) if $stderr;

	$rs |= execute("$main::selityConfig{ENGINE_ROOT_DIR}/tools/selity-net-interfaces-mngr start", \$stdour, \$stderr);
	debug($stdour) if $stdour;
	error($stderr) if $stderr;

	$rs;
}

1;
