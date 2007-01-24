#undef _POSIX_SOURCE
#include <Python.h>
/* cap_set_proc, etc. */
#include <sys/capability.h>
/* perror */
#include <stdio.h>
/* errno */
#include <errno.h>
/* strerror */
#include <string.h>
/* setuid, getuid */
#include <sys/types.h>
#include <unistd.h>
#include <stdbool.h>
#include <sys/prctl.h>
#include <grp.h>

#define DEBUG(p) if (!debug(p)) return NULL
#define DEFAULT_ID 65535

static bool debug(int value)
{
  if (value < 0) {
    PyErr_SetFromErrno(PyExc_OSError);
    return false;
  }
  else {
    return true;
  }
}

static PyObject *capability_droppriv(PyObject *self, PyObject *args)
{
  cap_value_t capvals[1] = { CAP_SYS_CHROOT };
  cap_t cap = cap_get_proc();
  const unsigned int capvals_num = sizeof(capvals) / sizeof(cap_value_t);
  unsigned int uid = DEFAULT_ID, gid = DEFAULT_ID;

  if (!PyArg_ParseTuple(args, "ii:capability", &uid, &gid)) {
    return NULL;
  }

  const gid_t groups[1] = { gid };

  DEBUG(prctl(PR_SET_KEEPCAPS, 1));
  DEBUG(setregid(gid, gid));
  DEBUG(setgid(gid));
  DEBUG(setgroups(1, groups));
  DEBUG(setuid(uid));
  DEBUG(setreuid(uid, uid));
  DEBUG(cap_clear(cap));
  DEBUG(cap_set_flag(cap, CAP_EFFECTIVE, capvals_num, capvals, CAP_SET));
  DEBUG(cap_set_flag(cap, CAP_PERMITTED, capvals_num, capvals, CAP_SET));
/*   DEBUG(cap_set_flag(cap, CAP_INHERITABLE, capvals_num, capvals, CAP_SET)); */
  DEBUG(cap_set_proc(cap));
  DEBUG(cap_free(cap));
  DEBUG(prctl(PR_SET_KEEPCAPS, 0));
  Py_RETURN_NONE;
}

static PyMethodDef capability_methods[] = {
  {"droppriv", capability_droppriv, METH_VARARGS,
   "Drop to non-su, maintaining CAP_SYS_CHROOT (the ability to chroot)."},
  {NULL, NULL, 0, NULL}
};

PyMODINIT_FUNC initcapability(void)
{
  (void) Py_InitModule("capability", capability_methods);
}
