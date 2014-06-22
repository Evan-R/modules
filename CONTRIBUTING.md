#Selene

For detailed licensing information, please refere to the [license file](LICENSE.md) that's distributed
with this package. 


## Rules in general

- be polite
- use the [psr-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standard
- leverage simplicity over complexity
- avoid complex nesting levels in class members (goal is max 2 - 3, avoid `else`) 
- bugfixes must provide a testcase.
- new features/aspects must provide a test case.

## Rules in particular

- don't address pull requests to components marked as subsplit. Instead
  address them to the main repository.  
- Never make pull requests from a master branch. Instead use a feature/hotfix
  branch.  

PRs that don't adhere to the rules above will be rejected without further
  notice.  


## Cloning the repository

```bash
$ git clone https://github.com/seleneapp/components ~/<destination>
```
