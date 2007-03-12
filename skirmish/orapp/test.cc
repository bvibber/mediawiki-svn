/*
 * $Id$
 */

#include <stdio.h>
#include <stdlib.h>
#include <getopt.h>
#include <unistd.h>

extern "C" {
#include <oci.h>
}

#include "orapp.hh"


/*
 * PURPOSE:
 *
 *   This is a test script that creates a trigger, sequence and table
 *   with ``test_'' as a prefix (to avoid any collisions with
 *   pre-existing tables on the specific TNS), and tests various
 *   functionality of the ORAPP API against it.  When completed, the
 *   program will clean up after itself and should leave the TNS in
 *   the original state (before the script was run).
 *
 *   Please note that if the test program does not complete for
 *   whatever reason (crash, network failure, valgrind), it will clean
 *   up on startup also, in case any of the old ``test_*'' stuff is
 *   laying around.
 */


const char *table_DROP = "DROP TABLE test_TABLE";
const char *table_CREATE =
        "CREATE TABLE test_TABLE (\n"
        "       id      NUMBER          DEFAULT '0'    NOT NULL,\n"
        "       str     VARCHAR2(250)   DEFAULT ''     NOT NULL\n"
        ")";

const char *sequence_DROP = "DROP SEQUENCE test_SEQUENCE";
const char *sequence_CREATE =
        "CREATE SEQUENCE test_SEQUENCE\n"
        "INCREMENT BY 1 START WITH 1 NOMINVALUE NOMAXVALUE NOCACHE";

const char *trigger_DROP = "DROP TRIGGER test_TRIGGER";
const char *trigger_CREATE =
        "CREATE OR REPLACE TRIGGER test_TRIGGER\n"
        "BEFORE INSERT ON test_TABLE\n"
        "FOR EACH ROW\n"
        "BEGIN\n"
        "    SELECT test_SEQUENCE.NEXTVAL INTO :NEW.id FROM DUAL;\n"
        "END;";


bool orapp_connect(ORAPP::Connection &db, const char *tns, const char *user, const char *pass) {
    printf("*** connecting to %s/%s@%s\n", user, pass, tns);

    if (!db.connect(tns, user, pass)) {
        printf("   >>> (failed to connect) %s\n", db.error().c_str());
        return false;
    }

    printf("*** connected to: %s\n", db.version().c_str());

    return true;
}

bool orapp_disconnect(ORAPP::Connection &db) {
    printf("*** disconnecting...\n");

    if (!db.disconnect()) {
        printf("   >>> (failed to disconnect)\n");
        return false;
    }

    return true;
}

bool orapp_setup(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    printf("*** setting up test_TABLE (SAFE TO IGNORE ERRORS ABOUT MISSING OBJECTS)\n");

    if (!q->execute(trigger_DROP))
        printf("   >>> (orapp_setup) %s [%s]\n", db.error().c_str(), q->statement());

    if (!q->execute(sequence_DROP))
        printf("   >>> (orapp_setup) %s [%s]\n", db.error().c_str(), q->statement());

    if (!q->execute(table_DROP))
        printf("   >>> (orapp_setup) %s [%s]\n", db.error().c_str(), q->statement());

    if (!q->execute(table_CREATE)   ||
        !q->execute(sequence_CREATE) ||
        !q->execute(trigger_CREATE)) {
        printf("   >>> (orapp_setup) %s [%s]\n", db.error().c_str(), q->statement());
        return false;
    }

    printf("*** done\n");

    return true;
}

bool orapp_unsetup(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    printf("*** tearing down test_TABLE\n");

    if (!q->execute(trigger_DROP)  ||
        !q->execute(sequence_DROP) ||
        !q->execute(table_DROP)) {
        printf("   >>> (orapp_unsetup) %s [%s]\n", db.error().c_str(), q->statement());
        return false;
    }

    printf("*** done\n");

    return true;
}


void orapp_select_straight(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "SELECT id, str FROM test_TABLE ";

    printf("*** executing SELECT: %s\n", q->statement());

    if (!q->execute()) {
        printf("   >>> (select failed) %s\n", db.error().c_str());
        return;
    }

    ORAPP::Row *r;

    while ((r = q->fetch())) {
        printf("::: rows fetched = %u, width = %u\n", q->rows(), r->width());

        unsigned i;
        for (i = 0; i < r->width(); i++)
            printf(":::    row%u named %s\n", i, r->name(i));

        i = 42;
        printf(":::    row%u named %s (should be NULL)\n", i, r->name(i));

        printf(":::    (name) id = %u, str = [%s], unknown = [%s]\n",
               (unsigned)(*r)["id"], (const char *)(*r)["str"], (const char *)(*r)["unknown"]);
        printf(":::    (row)  id = %u, str = [%s], unknown = [%s]\n",
               (unsigned)(*r)[0], (const char *)(*r)[1], (const char *)(*r)[50]);
    }

    printf("*** done\n");
}

void orapp_select_bind_read(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "SELECT id, str "
       << "FROM test_TABLE "
       << "WHERE id = :id";

    printf("*** executing SELECT: %s\n", q->statement());

    unsigned id = 1;

    q->bind(":id", id);

    if (!q->execute()) {
        printf("   >>> (select failed) %s\n", db.error().c_str());
        return;
    }

    ORAPP::Row *r;

    while ((r = q->fetch())) {
        printf("::: rows fetched = %u\n", q->rows());
        printf(":::    (name) id = %u, str = [%s], unknown = [%s]\n",
               (unsigned)(*r)["id"], (const char *)(*r)["str"], (const char *)(*r)["unknown"]);
        printf(":::    (row)  id = %u, str = [%s], unknown = [%s]\n",
               (unsigned)(*r)[0], (const char *)(*r)[1], (const char *)(*r)[50]);
    }

    printf("*** done\n");
}

void orapp_select_bind_write(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "BEGIN "
       << " SELECT id, str INTO :id, :str "
       << " FROM test_TABLE "
       << " WHERE id = 2;"
       << "END;";

    unsigned id;
    char buf[20] = {0};

    printf("*** executing SELECT: %s\n", q->statement());

    q->bind(":id", id);
    q->bind(":str", buf, sizeof(buf));

    if (!q->execute()) {
        printf("   >>> (select failed) %s\n", db.error().c_str());
        return;
    }

    printf("::: id = %u, str = [%s]\n", id, buf);

    printf("*** done\n");
}


void orapp_select_bind_both(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "BEGIN "
       << " SELECT id, str INTO :id, :str "
       << " FROM test_TABLE "
       << " WHERE id = :in_id;"
       << "END;";

    printf("*** executing SELECT: %s\n", q->statement());

    unsigned in_id = 3, id;
    char buf[20] = {0};

    q->bind(":in_id", in_id);
    q->bind(":id", id);
    q->bind(":str", buf, sizeof(buf));

    if (!q->execute()) {
        printf("   >>> (select failed) %s\n", db.error().c_str());
        return;
    }

    printf("::: id = %u, str = [%s]\n", id, buf);

    printf("*** done\n");
}

void orapp_update_straight(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "UPDATE test_TABLE SET str = 'meep meep' WHERE id = 9";

    printf("*** executing UPDATE: %s\n", q->statement());

    if (!q->execute()) {
        printf("   >>> (update failed) %s\n", db.error().c_str());
        return;
    }

    printf("*** done\n");
}

void orapp_update_bind_read(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "UPDATE test_TABLE SET str = 'meep meep' WHERE id = :id";

    printf("*** executing UPDATE: %s\n", q->statement());

    unsigned id = 10;

    q->bind(":id", id);

    if (!q->execute()) {
        printf("   >>> (update failed) %s\n", db.error().c_str());
        return;
    }

    printf("*** done\n");
}

void orapp_update_bind_write(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "UPDATE test_TABLE SET str = :str WHERE id = 11";

    printf("*** executing UPDATE: %s\n", q->statement());

    char *buf = "ooga booga";

    q->bind(":str", buf);

    if (!q->execute()) {
        printf("   >>> (update failed) %s\n", db.error().c_str());
        return;
    }

    printf("*** done\n");
}

void orapp_update_bind_both(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "UPDATE test_TABLE SET str = :str WHERE id = :id";

    printf("*** executing UPDATE: %s\n", q->statement());

    unsigned id = 12;
    char *buf = "ooga booga";

    q->bind(":id", id);
    q->bind(":str", buf);

    if (!q->execute()) {
        printf("   >>> (update failed) %s\n", db.error().c_str());
        return;
    }

    printf("*** done\n");
}

void orapp_delete_straight(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "DELETE from test_TABLE WHERE id = 14";

    printf("*** executing DELETE: %s\n", q->statement());

    if (!q->execute()) {
        printf("   >>> (delete failed) %s\n", db.error().c_str());
        return;
    }

    printf("*** done\n");
}

void orapp_delete_bind(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "DELETE from test_TABLE WHERE id = :id";

    printf("*** executing DELETE: %s\n", q->statement());

    unsigned id = 15;

    q->bind(":id", id);

    if (!q->execute()) {
        printf("   >>> (delete failed) %s\n", db.error().c_str());
        return;
    }

    printf("*** done\n");
}

void orapp_populate_table(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    printf("*** populating table\n");

    if (!q->execute("INSERT INTO test_TABLE (str) VALUES ('one')")   ||
        !q->execute("INSERT INTO test_TABLE (str) VALUES ('two')")   ||
        !q->execute("INSERT INTO test_TABLE (str) VALUES ('three')") ||
        !q->execute("INSERT INTO test_TABLE (str) VALUES ('four')"))
        printf("   >>> (populate failed) %s\n", db.error().c_str());
}

void orapp_wipe_table(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    printf("*** wiping table\n");

    q->execute("DELETE from test_TABLE");
}

void orapp_insert_straight(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "INSERT INTO test_TABLE "
       << "       (str) "
       << "VALUES ('hi mom')";

    printf("*** executing INSERT: %s\n", q->statement());
    if (!q->execute())
        printf("   >>> (insert failed) %s\n", db.error().c_str());

    *q << "INSERT INTO test_TABLE "
       << "       (str) "
       << "VALUES ('bye mom')";

    printf("*** executing INSERT: %s\n", q->statement());
    if (!q->execute())
        printf("   >>> (insert failed) %s\n", db.error().c_str());

    printf("*** done\n");
}

void orapp_insert_bind(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    *q << "INSERT INTO test_TABLE (str) VALUES (:str)";

    printf("*** executing INSERT: %s\n", q->statement());

    char *buf1 = "ooga";

    q->bind(":str", buf1);

    if (!q->execute())
        printf("   >>> (insert failed) %s\n", db.error().c_str());

    printf("*** executing INSERT: %s\n", q->statement());

    char *buf2 = "booga";

    *q << "INSERT INTO test_TABLE (str) VALUES (:str)";

    q->bind(":str", buf2);

    if (!q->execute())
        printf("   >>> (insert failed) %s\n", db.error().c_str());

    printf("*** done\n");
}

void orapp_feature_assign(ORAPP::Connection &db) {
    ORAPP::Query *q = db.query();

    q->assign("SELECT %u AS one, %f AS two, %s AS three FROM DUAL", 1, 2., "3");

    printf("*** executing SELECT: %s\n", q->statement());

    if (!q->execute()) {
        printf("   >>> (select failed) %s\n", db.error().c_str());
        return;
    }

    ORAPP::Row *r = q->fetch();
    if (!r) {
        printf("   >>> failed to get row\n");
        return;
    }

    printf("::: one = %s, two = %s, three = %s, unknown = [%s]\n",
           (const char *)(*r)["one"],  (const char *)(*r)["two"],
           (const char *)(*r)["three"],(const char *)(*r)["unknown"]);

    printf("*** done\n");
}

void logfunc(const char *s) {
    printf("   >>> [%s]\n", s);
}

int usage(const char *argv) {
    printf("usage: %s <-hc> <-U username> <-P password> <-T TNSname>\n", argv);

    printf("\t-h\tusage information (what you're reading)\n");
    printf("\t-c\trun test continuously (until ^C)\n");
    printf("\t-U\tusername with table, sequence and trigger privileges\n");
    printf("\t-P\tpassword with username (won't accept empty)\n");
    printf("\t-T\tTNSname of DB to use for tests\n");

    return 1;
}

void give_explanation(void) {

    printf("\n"
           " *** \n"
           " NOTE: The purpose of this program is to exercise the features of the \n"
           "       ORAPP API.  If it does not terminate in error, then all tests  \n"
           "       completed successfully.  This program will emit specific error \n"
           "       messages if any occur.  \n"
           " *** \n"
           "\n");

    sleep(5);

}

int main(int argc, char **argv) {
    if (getenv("ORACLE_HOME") == NULL) {
        printf("error: ORACLE_HOME unset, bailing..\n");
        return 1;
    }

    const char *tns = "", *user = "", *pass = "";
    bool continuous = false;

    char c;
    while ((c = getopt(argc, argv, "hcT:U:P:")) != EOF)
        switch (c) {
            case 'T': tns = optarg;  break;
            case 'U': user = optarg; break;
            case 'P': pass = optarg; break;
            case 'c': continuous = true; break;
            default: return usage(argv[0]);
        }

    if (!*tns || !*user || !*pass) {
        printf("ERROR: missing tnsname, username or password\n");
        return usage(argv[0]);
    }

    give_explanation();

    ORAPP::log_to(logfunc);
    ORAPP::Connection db;

    printf("--- ORAPP API v%s\n", ORAPP::VERSION);

    if (!orapp_connect(db, tns, user, pass))
        return 1;

    do {
        if (!orapp_setup(db))
            return 1;

        /*
         * First, some basic SELECT tests.
         */

        orapp_populate_table(db);

        orapp_select_straight(db);
        orapp_select_bind_read(db);
        orapp_select_bind_write(db);
        orapp_select_bind_both(db);

        orapp_wipe_table(db);

        /*
         * Test INSERT functionality.
         */

        orapp_insert_straight(db);
        orapp_insert_bind(db);

        orapp_select_straight(db);

        orapp_wipe_table(db);

        /*
         * Test UPDATE functionality.
         */

        orapp_populate_table(db);

        orapp_update_straight(db);
        orapp_update_bind_read(db);
        orapp_update_bind_write(db);
        orapp_update_bind_both(db);

        orapp_select_straight(db);

        orapp_wipe_table(db);

        /*
         * Test DELETE functionality.
         */

        orapp_populate_table(db);

        orapp_delete_straight(db);
        orapp_delete_bind(db);

        orapp_select_straight(db);

        orapp_wipe_table(db);

        /*
         * Ooh, neato.
         */

        orapp_feature_assign(db);

        /*
         * WE OUT.
         */

        if (!orapp_unsetup(db))
            return 1;

    } while (continuous);

    if (!orapp_disconnect(db))
        return 1;

    printf("--- done\n");

    return 0;
}


