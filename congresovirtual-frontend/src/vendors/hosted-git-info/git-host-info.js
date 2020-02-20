'use strict'

var gitHosts = module.exports = {
  github: {
    // First two are insecure and generally shouldn't be used any more, but
    // they are still supported.
    'protocols': [ 'git', 'http', 'git+ssh', 'git+https', 'ssh', 'https' ],
    'domain': 'github.com',
    'treepath': 'tree',
    'filetemplate': 'https://{auth@}raw.githubusercontent.com/{user}/{projects}/{committish}/{path}',
    'bugstemplate': 'https://{domain}/{user}/{projects}/issues',
    'gittemplate': 'git://{auth@}{domain}/{user}/{projects}.git{#committish}',
    'tarballtemplate': 'https://codeload.{domain}/{user}/{projects}/tar.gz/{committish}'
  },
  bitbucket: {
    'protocols': [ 'git+ssh', 'git+https', 'ssh', 'https' ],
    'domain': 'bitbucket.org',
    'treepath': 'src',
    'tarballtemplate': 'https://{domain}/{user}/{projects}/get/{committish}.tar.gz'
  },
  gitlab: {
    'protocols': [ 'git+ssh', 'git+https', 'ssh', 'https' ],
    'domain': 'gitlab.com',
    'treepath': 'tree',
    'bugstemplate': 'https://{domain}/{user}/{projects}/issues',
    'tarballtemplate': 'https://{domain}/{user}/{projects}/repository/archive.tar.gz?ref={committish}'
  },
  gist: {
    'protocols': [ 'git', 'git+ssh', 'git+https', 'ssh', 'https' ],
    'domain': 'gist.github.com',
    'pathmatch': /^[/](?:([^/]+)[/])?([a-z0-9]+)(?:[.]git)?$/,
    'filetemplate': 'https://gist.githubusercontent.com/{user}/{projects}/raw{/committish}/{path}',
    'bugstemplate': 'https://{domain}/{projects}',
    'gittemplate': 'git://{domain}/{projects}.git{#committish}',
    'sshtemplate': 'git@{domain}:/{projects}.git{#committish}',
    'sshurltemplate': 'git+ssh://git@{domain}/{projects}.git{#committish}',
    'browsetemplate': 'https://{domain}/{projects}{/committish}',
    'browsefiletemplate': 'https://{domain}/{projects}{/committish}{#path}',
    'docstemplate': 'https://{domain}/{projects}{/committish}',
    'httpstemplate': 'git+https://{domain}/{projects}.git{#committish}',
    'shortcuttemplate': '{type}:{projects}{#committish}',
    'pathtemplate': '{projects}{#committish}',
    'tarballtemplate': 'https://{domain}/{user}/{projects}/archive/{committish}.tar.gz',
    'hashformat': function (fragment) {
      return 'file-' + formatHashFragment(fragment)
    }
  }
}

var gitHostDefaults = {
  'sshtemplate': 'git@{domain}:{user}/{projects}.git{#committish}',
  'sshurltemplate': 'git+ssh://git@{domain}/{user}/{projects}.git{#committish}',
  'browsetemplate': 'https://{domain}/{user}/{projects}{/tree/committish}',
  'browsefiletemplate': 'https://{domain}/{user}/{projects}/{treepath}/{committish}/{path}{#fragment}',
  'docstemplate': 'https://{domain}/{user}/{projects}{/tree/committish}#readme',
  'httpstemplate': 'git+https://{auth@}{domain}/{user}/{projects}.git{#committish}',
  'filetemplate': 'https://{domain}/{user}/{projects}/raw/{committish}/{path}',
  'shortcuttemplate': '{type}:{user}/{projects}{#committish}',
  'pathtemplate': '{user}/{projects}{#committish}',
  'pathmatch': /^[/]([^/]+)[/]([^/]+?)(?:[.]git|[/])?$/,
  'hashformat': formatHashFragment
}

Object.keys(gitHosts).forEach(function (name) {
  Object.keys(gitHostDefaults).forEach(function (key) {
    if (gitHosts[name][key]) return
    gitHosts[name][key] = gitHostDefaults[key]
  })
  gitHosts[name].protocols_re = RegExp('^(' +
    gitHosts[name].protocols.map(function (protocol) {
      return protocol.replace(/([\\+*{}()[\]$^|])/g, '\\$1')
    }).join('|') + '):$')
})

function formatHashFragment (fragment) {
  return fragment.toLowerCase().replace(/^\W+|\/|\W+$/g, '').replace(/\W+/g, '-')
}
